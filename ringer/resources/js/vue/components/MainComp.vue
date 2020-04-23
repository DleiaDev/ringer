<template>
  <div id="app" class="container">

    <!-- Card -->
    <div class="card">

      <!-- Main screen -->
      <div id="main-screen">

        <!-- Header -->
        <div class="header">
          <div class="image" id="header-photo" :style="`background-image: url(${$user.photo})`" @click="showProfile"></div>
          <div id="add-contact-icon" @click="showAddContact">
            <i class="fas fa-user-plus icon"></i>
          </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="contacts-tab" data-toggle="tab" href="#contacts" role="tab" aria-controls="contacts" aria-selected="true">
              <i class="fas fa-comments"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="chatroom-tab" data-toggle="tab" href="#chatroom" role="tab" aria-controls="chatroom" aria-selected="true">
              <i class="fas fa-users"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="calls-tab" data-toggle="tab" href="#calls" role="tab" aria-controls="calls" aria-selected="true">
              <i class="fas fa-phone"></i>
            </a>
          </li>
        </ul>

        <!-- Tab content -->
        <section class="tab-content">
          <!-- Contacts -->
          <div id="contacts" class="tab-pane show active" role="tabpanel" aria-labelledby="contacts-tab">
            <ContactsList
              :contacts="contacts"
              @selected="getMessages"
            />
          </div>
          <!-- Chatroom -->
          <div id="chatroom" class="tab-pane" role="tabpanel" aria-labelledby="chatroom-tab">
            <h3>Chat Rooms</h3>
            <p>Coming soon!</p>
          </div>
          <!-- Calls -->
          <div id="calls" class="tab-pane" role="tabpanel" aria-labelledby="calls-tab">
            <h3>Calls</h3>
            <p>Comming soon!</p>
          </div>
        </section>

      </div>

      <component
        :is="currentTab"
        :contact="selectedContact"
        :messages="messages"
        @loadMore="loadMoreMessages"
        @messagesSeen="seeMessages"
        @back="goBack">
      </component>

    </div>

  </div>
</template>

<script>
import Welcome from './Welcome.vue'
import Profile from './Profile.vue'
import AddContact from './AddContact.vue'
import ContactsList from './ContactsList.vue'
import Conversation from './Conversation.vue'

export default {
  components: {ContactsList, Conversation, Welcome, Profile, AddContact},

  data() {
    return {
      currentTab: 'Welcome',
      contacts: {},
      messagesPerContact: {},
      messages: [],
      selectedContact: {},
      presenceChannel: null
    }
  },

  methods: {
    showAddContact() {
      this.messages = []
      this.selectedContact = {}
      this.currentTab = 'AddContact'
      $('.contact').removeClass('selected')
      if (screen.width <= 768)
        setTimeout(() => { $('.second-screen').animate({left: '0'}) }, 50)
    },
    showProfile() {
      this.messages = []
      this.selectedContact = {}
      this.currentTab = 'Profile'
      $('.contact').removeClass('selected')
      if (screen.width <= 768)
        setTimeout(() => { $('.second-screen').animate({left: '0'}) }, 50)
    },
    goBack(currentTab) {
      $('.contact').removeClass('selected')
      if (screen.width <= 768) {
        $('.second-screen').animate({left: '-150%'})
      } else {
        this.messages = []
        this.selectedContact = {}
        this.currentTab = 'Welcome'
      }
    },
    getContact(id) {
      axios.get(`/api/contacts/${id}`)
        .then(response => {
          response.data.online = true
          this.$set(this.contacts, id, response.data)
        })
    },
    getMessages(contactID) {
      $(`#contact-${contactID}`).addClass('selected')

      this.currentTab = 'Conversation'

      this.messages = []
      this.selectedContact = this.contacts[contactID]

      if (this.messagesPerContact[contactID]) {
        setTimeout(() => {
          this.messages = this.messagesPerContact[contactID]
          if (screen.width <= 768) $('.second-screen').animate({left: '0'})
        }, 50)
        return this.seeMessages(this.selectedContact)
      }

      this.$progress.start()
      axios.get(`/api/contacts/${contactID}/messages`)
      .then(response => {
        this.$progress.end()
        this.messages = response.data
        this.$set(this.messagesPerContact, contactID, response.data)
        this.seeMessages(this.selectedContact)
        if (response.data.length < 50)
          this.contacts[contactID].hasNoMoreMessages = true
        if (screen.width <= 768) $('.second-screen').animate({left: '0'})
      })
    },
    loadMoreMessages(contactID) {
      this.$progress.start()
      this.contacts[contactID].scrolls++
      var scrolls = this.contacts[contactID].scrolls
      axios.get(`/api/contacts/${contactID}/messages?scrolls=${scrolls}`)
      .then(response => {
        this.$progress.end()
        this.messages = response.data.concat(this.messages)
        this.messagesPerContact[contactID] = response.data.concat(this.messagesPerContact[contactID])
        if (response.data.length < 50)
          this.contacts[contactID].hasNoMoreMessages = true
      })
    },
    seeMessages(selectedContact) {
      if (selectedContact.lastMessage.to != this.$user.id) return
      var contactID = selectedContact.id
      this.messagesPerContact[contactID].forEach(message => message.read = true)
      this.contacts[contactID].lastMessage.read = true
      this.contacts[contactID].unread = 0
      axios.post('/api/seen', {
        currentUser_id: this.$user.id,
        contact_id: contactID
      })
    },
    handleIncoming(message) {
      var contactID = message.from

      if (!this.contacts[contactID])
        return this.getContact(message.from)

      // This also pushes into this.messages somehow?
      if (this.messagesPerContact[contactID])
        this.messagesPerContact[contactID].push(message)

      this.contacts[contactID].unread++
      this.contacts[contactID].lastMessage = message

      axios.post('/api/receive', {
        contactWhoSent: message.from,
        message_id: message.id
      })
    }
  },

  created() {

    Echo.private(`messages.${this.$user.id}`)
    .listen('.NewMessageEvent', event => {
      event.message.received = true
      if (this.selectedContact.id === event.message.from)
        event.message.read = true
      this.handleIncoming(event.message)
    })
    .listen('.MessageReceivedEvent', event => {
      if (this.selectedContact.id != event.contact_id) return
      this.messages.forEach(message => message.received = true)
    })
    .listen('.MessagesSeenEvent', event => {
      var contactID = event.contact_id
      this.contacts[contactID].lastMessage.read = true
      if (this.messagesPerContact[contactID])
        this.messagesPerContact[contactID].forEach(message => message.read = true)
      if (this.selectedContact.id === contactID)
        this.messages.forEach(message => message.read = true)
    })

    this.$progress.start()
    this.presenceChannel = Echo.join('presence-status')
    .here(onlineUsers => {
      axios.get('/api/contacts')
      .then(response => {
        this.$progress.end()
        response.data.forEach(contact => {
          onlineUsers.forEach(onlineUser => {
            if (contact.id === onlineUser.id)
              contact.online = true
          })
          this.$set(this.contacts, contact.id, contact)
        })
      })
    })
    .joining(user => {
      if (this.contacts[user.id])
        this.contacts[user.id].online = true

      // Receive all messages from this contact
      if (this.messagesPerContact[user.id])
        this.messagesPerContact[user.id].forEach(message => message.received = true)
      if (this.selectedContact.id === user.id)
        this.messages.forEach(message => message.received = true)
    })
    .leaving(user => {
      if (this.contacts[user.id])
        this.contacts[user.id].online = false
    })

  }

}
</script>
