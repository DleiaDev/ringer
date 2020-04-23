<template>
  <ul id="contacts-list" v-if="contactsArray.length">
    <li v-for="contact in contactsArray" :key="contact.id" @click="selectContact(contact.id)" class="contact" :id="`contact-${contact.id}`">

      <div class="left contact-photo">
        <div class="image background" :style="`background-image: url(${contact.photo})`"></div>
        <div class="status" :class="[contact.online ? 'online' : 'offline']"></div>
      </div>

      <div class="middle">
        <div class="name">{{ contact.name }}</div>
        <div class="message">
          {{ contact.lastMessage.text | shorten }}
        </div>
      </div>

      <div class="right">
        <div class="time" :class="{colored : $user.id == contact.lastMessage.to && !contact.lastMessage.read}">
          {{ contact.lastMessage.created_at | relative }}
        </div>
        <span class="badge badge-success unread" v-if="contact.unread">
          {{ contact.unread }}
        </span>
        <span v-if="$user.id == contact.lastMessage.from && contact.lastMessage.read" class="seen">
          <i class="fas fa-check icon"></i>
          <i class="fas fa-check icon"></i>
        </span>
      </div>

    </li>
  </ul>
</template>

<script>
export default {
  props: {
    contacts: Object
  },

  data() {
    return {
      contactsArray: []
    }
  },

  // Create 'contactsArray' on first run and sort it whenever 'contacts' object changes
  watch: {
    contacts: {
      handler: function(contacts) {
        this.contactsArray = Object.values(contacts)
        this.sortContacts()
      },
      deep: true
    }
  },

  methods: {
    sortContacts() {
      this.contactsArray.sort((a, b) => {
        return new Date(b.lastMessage.created_at) - new Date(a.lastMessage.created_at)
      })
    },
    selectContact(contact) {
      $('.contact').removeClass('selected')
      $(`#contact-${contact.id}`).addClass('selected')
      this.$emit('selected', contact);
    }
  }

}
</script>
