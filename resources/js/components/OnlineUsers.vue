<template>
  <ul class="list-group list-group-flush" v-for="(member, id) in channelMembers" :key="id">
    <li class="list-group-item">{{ member.user.username }}</li>
  </ul>
</template>

<script>
export default {};
</script>
