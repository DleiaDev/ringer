<template>
  <div class="second-screen">

    <!-- Conversation -->
    <div id="conversation" v-if="messages.length">

      <div class="header">
        <a class="back" @click="$emit('back')">
          <i class="fas fa-arrow-left"></i>
        </a>
        <div class="right">
          <div class="image" :style="`background-image: url(${contact.photo})`"></div>
          <div class="name">
            {{ contact.name }}
          </div>
        </div>
      </div>

      <ul ref="feed">
        <button class="btn btn-primary" id="load-more-btn" v-if="!contact.hasNoMoreMessages" @click="loadMoreMessages">
          Load more
        </button>
        <li
          v-for="(message, index) in messages"
          :class="['message', message.from === contact.id ? 'received' : 'sent']">
          <div
            class="text"
            data-toggle="tooltip"
            :data-placement="message.from === contact.id ? 'right' : 'left'"
            :title="message.created_at | relative"
          >
            {{ message.text }}
            <span class="status" v-if="message.from === $user.id">
              <span class="seen" v-if="message.read">
                <i class="fas fa-check icon"></i>
                <i class="fas fa-check icon"></i>
              </span>
              <i class="fas fa-check-circle" v-else-if="message.received"></i>
              <i class="far fa-check-circle" v-else-if="message.sent"></i>
              <i class="far fa-circle" v-else></i>
            </span>
          </div>
        </li>
      </ul>

      <div class="typing">
        Typing...
      </div>

      <div class="message-composer">
        <input
          id="message-box"
          v-model="text"
          class="form-control"
          placeholder="Your message..."
          @keydown.enter="sendMessage"
          @click="$emit('messagesSeen', contact)"
          @focus="$emit('messagesSeen', contact)"
          autocomplete="off"
          spellcheck="false">
        <i class="fab fa-telegram-plane icon" @click="sendMessage"></i>
      </div>

    </div>

  </div>
</template>

<script>
export default {
  props: {
    messages: Array,
    contact: Object
  },

  data() {
    return {
      text: '',
      shouldScroll: true,
      typingChannel: null
    }
  },

  methods: {
    scrollToBottom() {
      setTimeout(() => {
        this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight
      }, 50)
    },
    sendMessage() {
      if (!this.text) return

      this.messages.push({
        id: null,
        from: this.$user.id,
        to: this.contact.id,
        text: this.text,
        read: false,
        received: false,
        sent: false,
        created_at: moment(),
        updated_at: moment()
      })

      axios.post('/api/send', {
        contact_id: this.contact.id,
        text: this.text
      })
      .then(response => {
        var message = response.data
        this.messages.forEach(el => el.sent = true)
        this.$parent.contacts[message.to].lastMessage = message
      })

      this.text = ''
      this.shouldScroll = true
    },
    loadMoreMessages() {
      this.shouldScroll = false
      this.$emit('loadMore', this.contact.id)
    }
  },

  watch: {
    messages() {
      if (!this.messages.length) return

      if (this.shouldScroll) this.scrollToBottom()
      this.shouldScroll = this.contact.hasNoMoreMessages ? true : this.shouldScroll

      if (!this.typingChannel)
        this.typingChannel = Echo.private(`typing.${this.contact.id}`)

      $('.typing').hide()

      $('#message-box').focus()

      Vue.nextTick().then(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    },
    text() {
      if (this.text)
        this.typingChannel.whisper('client-typing', {from: this.$user.id})
      else
        this.typingChannel.whisper('client-stoptyping', {from: this.$user.id})
    }
  },

  created() {
    Echo.private(`typing.${this.$user.id}`)
    .listenForWhisper('client-typing', event => {
      if (this.contact.id === event.from) $('.typing').show()
    })
    Echo.private(`typing.${this.$user.id}`)
    .listenForWhisper('client-stoptyping', event => {
      if (this.contact.id === event.from) $('.typing').hide()
    })
  }

}
</script>
