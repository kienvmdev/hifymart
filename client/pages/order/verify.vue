<template>
  <v-container
    fill-height
    class="justify-center"
  >
    <v-card>
      <v-card-title>Thanh toán thành công</v-card-title>
      <v-card-text>
        <p>Cảm ơn bạn đã mua hàng tại {{ settings['app-name'] }}</p>
        <p>Mã đơn hàng: #{{ order.id }}</p>
      </v-card-text>
      <v-card-actions>
        <v-btn
          color="primary"
          :to="{ name: 'order-id', params: { id: order.id } }"
        >
          Xem đơn hàng
        </v-btn>
        <v-btn
          color="primary"
          to="/"
        >
          Quay lại shop
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  async asyncData({ app, query, error }) {
    try {
      let { success, order } = await app.$axios.$get('/payment/vnpay/process', { params: query });
      return {
        success: success,
        order: order
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
  computed: {
    ...mapGetters(['settings']),
  },
}
</script>
