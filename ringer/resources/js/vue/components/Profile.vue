<template>
  <div class="second-screen" id="edit-profile">

    <div class="header">
      <a class="back" @click="$emit('back', 'profile')">
        <i class="fas fa-arrow-left"></i>
      </a>
    </div>

    <!-- Profile form -->
    <form @submit.prevent="updateUser">
      <h3>General</h3>
      <!-- Photo -->
      <div class="image" id="profile-photo" :style="`background-image: url(${$user.photo})`">
        <input id="file" type="file" name="photo" @change="validatePhoto($event)">
      </div>
      <!-- Name -->
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-user icon"></i></span>
        </div>
        <input v-model="profileForm.name" type="text" name="name" autocomplete="off" spellcheck="false"
        placeholder="Name" class="form-control" :class="{ 'is-invalid' : profileForm.errors.has('name') }">
        <has-error :form="profileForm" field="name"></has-error>
      </div>
      <!-- Email -->
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-envelope icon"></i></span>
        </div>
        <input v-model="profileForm.email" type="email" name="email" autocomplete="off" spellcheck="false"
        placeholder="Email" class="form-control" :class="{ 'is-invalid' : profileForm.errors.has('email') }">
        <has-error :form="profileForm" field="email"></has-error>
      </div>
      <!-- Submit -->
      <div class="form-group">
        <button type="submit" class="mybtn1">Update</button>
      </div>
    </form>

    <!-- Password form -->
    <form @submit.prevent="changePassword">
      <h3>Security</h3>
      <!-- Current Password -->
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock icon"></i></span>
        </div>
        <input v-model="passwordForm.currentPassword" type="password"
        name="currentPassword" placeholder="Current password"
        class="form-control" :class="{ 'is-invalid' : passwordForm.errors.has('currentPassword') }">
        <has-error :form="passwordForm" field="currentPassword"></has-error>
      </div>
      <!-- New Password -->
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock icon"></i></span>
        </div>
        <input v-model="passwordForm.newPassword" type="password"
         name="newPassword" placeholder="New password"
         class="form-control" :class="{ 'is-invalid' : passwordForm.errors.has('newPassword') }">
        <has-error :form="passwordForm" field="newPassword"></has-error>
      </div>
      <!-- Repeat New Password -->
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-lock icon"></i></span>
        </div>
        <input v-model="passwordForm.newPassword_confirmation" type="password"
        name="newPassword_confirmation" placeholder="Confirm new password"
        class="form-control" :class="{ 'is-invalid' : passwordForm.errors.has('newPassword_confirmation') }">
        <has-error :form="passwordForm" field="newPassword_confirmation"></has-error>
      </div>
      <!-- Submit -->
      <div class="form-group">
        <button type="submit" class="mybtn1">Update</button>
      </div>
    </form>

    <form method="POST" action="/logout">
      <input type="hidden" name="_token" :value="csrf">
      <button type="submit" name="button" class="mybtn1" @click="$progress.start()">
        Logout
      </button>
    </form>

  </div>
</template>

<script>
export default {

  data() {
    return {
      profileForm: new Form({
        name: this.$user.name,
        email: this.$user.email
      }),
      passwordForm: new Form({
        currentPassword: '',
        newPassword: '',
        newPassword_confirmation: ''
      }),
      csrf: $('meta[name="csrf-token"]').attr('content')
    }
  },

  methods: {
    updateUser() {
      this.$progress.start()
      this.profileForm.post('/api/user')
      .then(response => {
        this.$user.name = response.data.name
        this.$user.email = response.data.email
        toast.fire({title: 'Changes have been saved.', icon: 'success'})
        this.$progress.end()
      })
      .catch(error => {
        toast.fire({title: 'An error occurred.', icon: 'error'})
        this.$progress.end()
      })
    },
    uploadPhoto(file) {
      this.$progress.start()
      var formData = new FormData()
      formData.append('photo', file)
      axios.post('/api/user/photo', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(response => {
        $('#profile-photo').css('background-image', `url(${response.data.photo})`)
        $('#header-photo').css('background-image', `url(${response.data.photo})`)
        this.$user.photo = response.data.photo
        toast.fire({title: 'Changes have been saved.', icon: 'success'})
        this.$progress.end()
      }).catch(error => {
        toast.fire({title: 'An error occurred.', icon: 'error'})
        this.$progress.end()
      })
    },
    validatePhoto(e) {
      var file = e.target.files[0]
      return this.uploadPhoto(file)
      if ((/\.(png|jpeg|jpg|svg)$/i).test(file.name)) {
        if (Math.round(file.size / (1024*1024)) < 2) {
          var fileReader = new FileReader()
          fileReader.onload = () => {
            var image = new Image()
            image.onload = () => {
              if (image.width >= 400 && image.height >= 400)
                this.uploadPhoto(file)
              else
                toast.fire({title: 'Image has to be at least 400x400.', icon: 'error'})
            }
            image.src = fileReader.result
          }
          fileReader.readAsDataURL(file)
        } else
          toast.fire({title: 'Image has to be less the 2MB.', icon: 'error'})
      } else
        toast.fire({title: 'Invalid image extension.', icon: 'error'})

      $('#file').val('')
    },
    changePassword() {
      this.$progress.start()
      this.passwordForm.put('/api/user/password')
        .then(() => {
          toast.fire({title: 'Password has been changed.', icon: 'success'})
          this.passwordForm.reset()
          this.$progress.end()
        })
        .catch(() => {
          toast.fire({title: 'An error occurred.', icon: 'error'})
          this.$progress.end()
        });
    },
  },

  mounted() {
    if (screen.width <= 768) {
      $('.second-screen').animate({left: '-0%'});
      $('.mybtn1').attr('class', 'mybtn2');
    }
  }
}
</script>

<style lang="css">
</style>
