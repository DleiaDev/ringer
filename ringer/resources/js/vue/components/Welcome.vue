<template>
  <div class="second-screen">

    <div id="welcome">
      <h3>Welcome, {{ $user.name }}!</h3>
      <div class="image" :style="`background-image: url(${$user.photo})`"></div>
      <p>Click on a contact to start chatting.</p>
      <p>To add new contact click on the user with a plus sign icon.</p>
    </div>

  </div>
</template>

<script>
export default {
}
</script>
