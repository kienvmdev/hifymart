<template>
  <v-container>
    <v-row>
      <v-col
        cols="12"
        md="8"
      >
        <v-alert
          v-if="order.status == 0"
          prominent
          type="info"
        >
          <v-row align="center">
            <v-col class="grow">
              Đây là đơn hàng mới và chưa được xác nhận!
            </v-col>
            <v-col class="shrink">
              <v-btn @click="approved">
                Xác nhận ngay
              </v-btn>
            </v-col>
          </v-row>
        </v-alert>
        <v-card>
          <v-card-title>Thông tin đơn hàng #{{ order.id }}</v-card-title>
          <v-card-text>
            <table>
              <thead style="color:#ffffff;background:#1976d2;text-align:left">
                <tr class="pa-2">
                  <th>Sản phẩm</th>
                  <th class="text-right">
                    SL
                  </th>
                  <th class="text-right">
                    Giá
                  </th>
                  <th class="text-right">
                    Tổng
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="product in order.products"
                  :key="product.id"
                >
                  <td class="d-flex align-center">
                    <img
                      :src="apiUrl + product.featured_image"
                      :alt="product.name"
                      width="100"
                      height="100"
                    >
                    <span class="overflow">{{ product.name }}</span>
                  </td>
                  <td class="text-right">
                    {{ product.pivot.quantity }}
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(product.pivot.price) }}
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(product.pivot.price * product.pivot.quantity) }}
                  </td>
                </tr>
              </tbody>
              <tfoot
                class="text-right"
                style="background-color: rgb(243, 243, 243);"
              >
                <tr>
                  <td
                    class="text-right"
                    colspan="3"
                  >
                    <b>Thành tiền</b>
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(order.billing_subtotal) }}
                  </td>
                </tr>
                <tr>
                  <td
                    class="text-right"
                    colspan="3"
                  >
                    <b>Giảm giá</b>
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(order.billing_discount) }}
                  </td>
                </tr>
                <tr>
                  <td
                    class="text-right"
                    colspan="3"
                  >
                    <b>Thuế</b>
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(order.billing_tax) }}
                  </td>
                </tr>
                <tr>
                  <td
                    class="text-right"
                    colspan="3"
                  >
                    <b>Phí vận chuyển</b>
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(order.billing_shipping_fee) }}
                  </td>
                </tr>
                <tr>
                  <td
                    class="text-right"
                    colspan="3"
                  >
                    <b>Tổng cộng</b>
                  </td>
                  <td class="text-right">
                    {{ $moneyFormat(order.billing_total) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <v-card-title>Thông tin khách hàng</v-card-title>
          <v-card-text>
            <div><b>Khách hàng: {{ order.user ? order.user.name : order.billing_name }}</b></div>
            <div>Email: {{ order.user ? order.user.email : order.billing_email }}</div>
            <div>SĐT: {{ order.user ? order.user.phone : order.billing_phone }}</div>
            <div v-if="order.user">
              Số đơn hàng: {{ order.user.orders_count }}
            </div>
          </v-card-text>
          <v-divider />
          <v-card-title>Địa chỉ giao hàng</v-card-title>
          <v-card-text>
            <div><b>Tên người nhận: {{ order.billing_name }}</b></div>
            <div>SĐT: {{ order.billing_phone }}</div>
            <div>Địa chỉ: {{ fullAddress(order) }}</div>
          </v-card-text>
          <v-divider />
          <v-card-title>Thông tin đơn hàng</v-card-title>
          <v-card-text>
            <div>Trạng thái: {{ statusText }}</div>
            <div>Thanh toán: <b>{{ payStatus }}</b></div>
            <div>Vận chuyển: {{ shipped ? 'Đã vận chuyển' : 'Chưa gửi' }}</div>
          </v-card-text>
          <v-divider />
          <v-card-text>
            <v-btn
              v-if="!order.is_paid"
              block
              class="ma-1"
              color="success"
              @click="setPaid"
            >
              Xác nhận đã thanh toán
            </v-btn>
            <v-btn
              v-if="!order.shipped"
              block
              class="ma-1"
              color="success"
              @click="setShipped"
            >
              Xác nhận đã vẫn chuyển
            </v-btn>
            <v-btn
              v-if="order.status!=3"
              block
              class="ma-1"
              color="success"
              @click="completed"
            >
              Xác nhận đơn hàng thành công
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  layout: 'admin',
  async asyncData({ app, params, error }) {
    try {
      let { order } = await app.$axios.$get('/admin/orders/' + params.id);
      return {
        order: order
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
  computed: {
    apiUrl() {
      return process.env.apiUrl
    },
    payStatus() {
      if (this.order.payment_type == 'cod') {
        return 'COD';
      }
      return (this.order.is_paid ? 'Đã thanh toán' : 'Chưa thanh toán') + `(${this.order.payment_type})`
    },
    statusText() {
      let output;
      switch (this.order.status) {
        case 0:
          output = 'Đơn hàng mới';
          break;
        case 1:
          output = 'Đang chờ xử lý';
          break;
        case 2:
          output = 'Đã/Đang vận chuyển';
          break;
        case 3:
          output = 'Đơn hàng thành công';
          break;
        default:
          output = 'Đóng';
          break;
      }
      return output;
    }
  },
  methods: {
    fullAddress(item) {
      return `${item.billing_address}, ${item.district.name}, ${item.province.name}`
    },
    baseAxios(data) {
      return new Promise((resolve, reject) => {
        this.$axios.patch("/admin/orders/" + this.$route.params.id, data)
        .then(() => {
          return resolve(this.$notifier.showMessage({
            content: 'Lưu thành công!',
            color: 'success',
            right: false
          }))
        })
        .catch(() => {
          return reject(this.$notifier.showMessage({
            content: 'Có lỗi, vui lòng thử lại',
            color: 'error',
            right: false
          }))
        })
      })
    },
    approved() {
      this.baseAxios({ status: 1 }).then(() => {
        this.order.status = 1;
      });
    },
    setPaid() {
      this.baseAxios({ is_paid: true }).then(() => {
        this.order.is_paid = true;
      });
    },
    setShipped() {
      this.baseAxios({ shipped: 1 }).then(() => {
        this.order.shipped = true;
      });
    },
    completed() {
      this.baseAxios({ status: 3 }).then(() => {
        this.order.status = 3;
      });
    }
  },
}
</script>
<style scoped>
      table {
        border-collapse: collapse;
        width: 100%;
      }

      td, th {
        text-align: left;
        padding: 8px;
      }

      tr {
        border: 1px solid #dddddd;
      }

      tr:nth-child(even) {
        background-color:#f3f3f3;
      }
      .overflow {
        margin: auto 0;
        margin-left:4px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }
</style>
