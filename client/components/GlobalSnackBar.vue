<template>
  <v-snackbar
    v-model="show"
    :color="color"
    :right="right"
  >
    {{ message }}
    <template #action="{ attrs }">
      <v-btn
        text
        v-bind="attrs"
        @click="show = false"
      >
        Đóng
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  data () {
    return {
      show: false,
      message: '',
      color: '',
      right: false
    }
  },
  created () {
    this.$store.subscribe((mutation, state) => {
      if (mutation.type === 'snackbar/showMessage') {
        this.message = state.snackbar.content;
        this.color = state.snackbar.color;
        this.right = state.snackbar.right
        this.show = true;
      }
    })
  }
}
</script>
