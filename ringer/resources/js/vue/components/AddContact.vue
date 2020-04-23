<template>
  <div class="second-screen" id="add-contact">

    <div class="header">
      <a class="back" @click="$emit('back', 'addcontact')">
        <i class="fas fa-arrow-left"></i>
      </a>
    </div>

    <div class="wrapper">
      <h3>Add Contact</h3>
      <p>Type in the email of a contact and hit enter on your keyboard.</p>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="fas fa-envelope icon"></i>
          </span>
        </div>
        <input class="form-control" v-model="email" type="email" autocomplete="off"
        spellcheck="false" placeholder="Email" @keydown.enter="sendRequest">
      </div>
    </div>

  </div>
</template>

<script>
export default {

  data() {
    return {
      email: '',
      searching: false
    }
  },

  methods: {
    sendRequest() {
      if (!this.email || this.searching) return

      this.$progress.start()

      this.searching = true

      axios.post('/api/contacts/add', { email: this.email })
        .then(response => {
          this.searching = false
          toast.fire({
            title: response.data.message,
            icon: response.data.status ? 'success' : 'error'
          })
          if (response.data.status) {
            var contact = response.data.contact
            contact.online = this.$parent.presenceChannel.subscription.members.members[contact.id] ? true : false
            this.$set(this.$parent.contacts, contact.id, contact)
          }
          this.$progress.end()
        }).catch(error => {
          this.$progress.end()
          this.searching = false
        })
    },
  }

}
</script>

<style lang="css">
</style>
