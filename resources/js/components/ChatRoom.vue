<template>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="card">
          <div class="card-header">Online Users</div>

          <div class="card-body">
            <ul
              class="list-group list-group-flush"
              v-for="(member, id) in channelMembers"
              :key="id"
            >
              <li class="list-group-item">{{ member.name }}</li>
            </ul>
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

            <form @submit.prevent="sendMessage" method="post">
              <div class="input-group">
                <input
                  type="text"
                  v-model="newMessage"
                  class="form-control"
                  placeholder="Type your message..."
                />

                <div class="input-group-append">
                  <button class="btn btn-primary">Send</button>
                </div>
              </div>
            </form>
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
      channelMembers: [],
      client: null,
      messages: [],
      newMessage: ""
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

    // listen for new messages
    this.channel.on("message.new", event => {
      this.messages.push({
        text: event.message.text,
        user: event.message.user
      });
    });

    // const channels = await this.client.queryChannels(
    //   { type: "messaging" },
    //   { last_message_at: -1 },
    //   { presence: true }
    // );

    // console.log(channels);
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
      this.channel = this.client.channel("messaging", "chatroom", {
        name: "Laravel Chatroom"
      });

      //   this.messages = (await this.channel.watch()).messages;
      const state = await this.channel.watch();

      this.messages = state.messages;

      this.channelMembers = state.members.filter(member => {
        return member.online === true;
      });

      console.log(state);
    },
    async sendMessage() {
      await this.channel.sendMessage({
        text: this.newMessage
      });

      this.newMessage = "";
    }
  }
};
</script>
