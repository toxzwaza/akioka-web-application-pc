<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { reactive, onMounted } from "vue";
import axios from "axios";
import MainTitle from "@/Components/Title/MainTitle.vue";

const props = defineProps({
  supplier: Object,
  edit: Boolean,
});

const form = reactive({
  supplier_id: null,
  supplier_no: null,
  name: null,
  rub_name: null,
  tel: null,
  fax: null,
  p_code: null,
  address: null,
  invoice_registration_number: null,
  memo: null,
});

const createSupplier = () => {
  console.log(form);
  axios
    .post(route("stock.suppliers.store"), form)
    .then((res) => {
      console.log(res.data);
      if (confirm("登録が完了しました。")) {
        window.location.href = route("stock");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const editSupplier = () => {
  console.log(form);
  axios
    .post(route("stock.suppliers.store"), form)
    .then((res) => {
      console.log(res.data);
      if (confirm("編集が完了しました。")) {
        window.location.href = route("stock");
      }
    })
    .catch((error) => {
      console.log(error);
    });
};
onMounted(() => {
  if (props.edit) {
    form.supplier_id = props.supplier.id;
    form.supplier_no = props.supplier.supplier_no;
    form.name = props.supplier.name;
    form.rub_name = props.supplier.rub_name;
    form.tel = props.supplier.tel;
    form.fax = props.supplier.fax;
    form.p_code = props.supplier.p_code;
    form.address = props.supplier.address;
    form.invoice_registration_number =
      props.supplier.invoice_registration_number;
    form.memo = props.supplier.memo;
  }
});
</script>
<template>
  <MainLayout :title="'取引先'">
    <template #content>
      <div>
        <section class="text-gray-600 body-font">
          <MainTitle
            :top="'取引先'"
            :sub="'取引先一覧の確認と登録を行います。必須項目を入力して、追加ボタンを押してください。'"
          />
          <div
            class="container mx-auto flex px-5 md:flex-row flex-col items-start justify-center"
          >
            <div class="bg-white py-6 sm:py-8 lg:py-12">
              <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
                <form class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2">
                  <div>
                    <label
                      for="supplier_no"
                      class="mb-2 inline-block text-sm text-gray-800 sm:text-base"
                      >取引先no</label
                    >
                    <input
                      v-model="form.supplier_no"
                      name="supplier_no"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label
                      for="name"
                      class="mb-2 inline-block text-sm text-red-400 sm:text-base"
                      >会社名*</label
                    >
                    <input
                      v-model="form.name"
                      name="name"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label
                      for="rub_name"
                      class="mb-2 inline-block text-sm text-gray-800 sm:text-base"
                      >読み（ひらがな）</label
                    >
                    <input
                      v-model="form.rub_name"
                      name="rub_name"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="">
                    <label
                      for="tel"
                      class="mb-2 inline-block text-sm sm:text-base"
                      >TEL</label
                    >
                    <input
                      v-model="form.tel"
                      name="tel"
                      type="text"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>
                  <div class="">
                    <label
                      for="fax"
                      class="mb-2 inline-block text-sm sm:text-base"
                      >FAX</label
                    >
                    <input
                      v-model="form.fax"
                      name="fax"
                      type="text"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label
                      for="address"
                      class="mb-2 inline-block text-sm text-gray-800 sm:text-base"
                      >住所</label
                    >
                    <input
                      v-model="form.address"
                      name="address"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="">
                    <label
                      for="p_code"
                      class="mb-2 inline-block text-sm sm:text-base"
                      >郵便番号</label
                    >
                    <input
                      v-model="form.p_code"
                      name="p_code"
                      type="text"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="">
                    <label
                      for="invoice_registration_number"
                      class="mb-2 inline-block text-sm sm:text-base"
                      >的確事業者番号</label
                    >
                    <input
                      v-model="form.invoice_registration_number"
                      name="invoice_registration_number"
                      type="text"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      value=""
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label
                      for="memo"
                      class="mb-2 inline-block text-sm text-gray-800 sm:text-base"
                      >メモ</label
                    >
                    <textarea
                      v-model="form.memo"
                      name="memo"
                      class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"
                      rows="4"
                    ></textarea>
                  </div>

                  <div class="flex items-center justify-between sm:col-span-2">
                    <button
                      v-if="!props.edit"
                      @click.prevent="createSupplier"
                      class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
                    >
                      追加
                    </button>
                    <button
                      v-else
                      @click.prevent="editSupplier"
                      class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base"
                    >
                      編集
                    </button>

                    <span class="text-sm text-gray-500">*Required</span>
                  </div>
                </form>
                <!-- form - end -->
              </div>
            </div>
          </div>
        </section>
      </div>
    </template>
  </MainLayout>
</template>
<style scoped lang="scss">
</style>
