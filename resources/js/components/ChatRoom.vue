<template>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="card">
          <div class="card-header">Members</div>

          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li
                class="list-group-item"
                v-for="(member, id) in members"
                :key="id"
              >{{ member.user.name }}</li>
            </ul>

            <button type="button" class="mt-4 btn btn-primary" @click="leaveChannel">Leave Channel</button>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card">
          <div class="card-header">Chats</div>

          <div class="card-body">
            <dl v-for="message in messages" v-bind:key="message.id">
              <dt :class="{  'text-right': message.user.id === username }">{{ message.user.name }}</dt>
              <dd :class="{  'text-right': message.user.id === username }">{{ message.text }}</dd>
            </dl>

            <hr />

            <span class="help-block" v-if="status" v-text="status" style="font-style: italic;"></span>

            <form @submit.prevent="sendMessage" method="post">
              <div class="input-group">
                <input
                  type="text"
                  v-model="newMessage"
                  class="form-control"
                  placeholder="Type your message..."
                  @keydown="startedTyping"
                  @keyup="stoppedTyping"
                />

                <div class="input-group-append">
                  <button class="btn btn-primary">Send</button>
                </div>
              </div>
            </form>

            <span
              class="help-block mt-2"
              v-if="isTyping && typing.user.id !== username"
              style="font-style: italic;"
            >{{ `${typing.user.name} is typing...` }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { StreamChat } from "stream-chat";

export default {
  name: "ChatRoom",
  props: {
    authUser: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      token: null,
      channel: null,
      members: [],
      client: null,
      messages: [],
      newMessage: "",
      status: "",
      isTyping: false,
      typing: null
    };
  },
  computed: {
    username() {
      return this.authUser.email.split("@")[0];
    }
  },
  async created() {
    // gerenrate clientside token from server
    await this.getToken();

    await this.initializeStream();
    await this.initializeChannel();
  },
  methods: {
    async getToken() {
      const { data } = await axios.post("/api/generate-token", {
        user_id: this.username
      });

      this.token = data.token;
    },
    async initializeStream() {
      this.client = new StreamChat(process.env.MIX_STREAM_API_KEY, {
        timeout: 9000
      });

      await this.client.setUser(
        { id: this.username, name: this.authUser.name },
        this.token
      );
    },
    async initializeChannel() {
      this.channel = this.client.channel("messaging", "chatroom");

      const { members, messages } = await this.channel.watch();

      this.members = members;

      this.messages = messages;

      // listen for new messages
      this.channel.on("message.new", event => {
        this.messages.push({
          text: event.message.text,
          user: event.message.user
        });
      });

      // listen for when a new member is added to channel
      this.channel.on("member.added", event => {
        this.members.push(event);

        this.status = `${event.user.name} joined the chat`;
      });

      // listen for when a member leaves channel
      this.channel.on("member.removed", event => {
        this.status = `${event.user.name} just left the chat`;
      });

      // listen for typing...
      this.channel.on("typing.start", event => {
        this.isTyping = true;
        this.typing = event;
      });

      this.channel.on("typing.stop", event => {
        this.isTyping = false;
      });
    },
    async sendMessage() {
      await this.channel.sendMessage({
        text: this.newMessage
      });

      this.newMessage = "";
    },
    leaveChannel() {
      axios.post("/api/leave-channel", {
        username: this.username
      });

      window.location.href = "/dashboard";
    },
    async startedTyping() {
      await this.channel.keystroke();
    },
    stoppedTyping() {
      setTimeout(async () => {
        await this.channel.stopTyping();
      }, 2000);
    }
  }
};
</script>
