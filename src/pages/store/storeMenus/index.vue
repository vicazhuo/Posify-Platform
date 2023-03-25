<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          inline
          ref="roleData"
          :model="roleData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="规则状态：">
            <Select
              v-model="roleData.is_show"
              placeholder="请选择"
              clearable
              @on-change="getData"
              style="width: 250px"
            >
              <Option value="1">显示</Option>
              <Option value="0">不显示</Option>
            </Select>
          </FormItem>
          <FormItem label="按钮名称：" prop="status2" label-for="status2">
            <Input
              v-model="roleData.keyword"
              placeholder="请输入按钮名称"
              style="width: 250px;margin-right:14px;"
            />
             <Button type="primary" @click="getData()">查询</Button>
          </FormItem>
          
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button
        v-auth="['setting-system_menus-add']"
        type="primary"
        @click="menusAdd('添加规则')"
        >添加规则</Button
      >
      <vxe-table
        :border="false"
        class="vxeTable ivu-mt"
        highlight-hover-row
        highlight-current-row
        :loading="loading"
        ref="xTable"
        row-id="id"
        header-row-class-name="false"
        :tree-config="{ children: 'children', reserve: true }"
        :data="tableData"
      >
        <vxe-table-column
          field="id"
          title="ID"
          tooltip
          min-width="70"
        ></vxe-table-column>
        <vxe-table-column
          field="menu_name"
          tree-node
          title="按钮名称"
          min-width="200"
        ></vxe-table-column>
        <vxe-table-column field="api_url" title="接口路径" min-width="150">
          <template v-slot="{ row }">
            <span>{{
              row.methods
                ? "[" + row.methods + "]  " + row.api_url
                : row.api_url
            }}</span>
          </template>
        </vxe-table-column>
        <vxe-table-column
          field="unique_auth"
          title="前端权限"
          min-width="300"
        ></vxe-table-column>
        <vxe-table-column
          field="menu_path"
          title="页面路由"
          min-width="240"
          tooltip="true"
        ></vxe-table-column>
        <vxe-table-column field="flag" title="规则状态" min-width="120">
          <template v-slot="{ row }">
            <i-switch
              v-if="row.auth_type == 1"
              v-model="row.is_show"
              :value="row.is_show"
              :true-value="1"
              :false-value="0"
              @on-change="onchangeIsShow(row)"
              size="large"
            >
              <span slot="open">显示</span>
              <span slot="close">隐藏</span>
            </i-switch>
          </template>
        </vxe-table-column>
        <vxe-table-column
          field="date"
          title="操作"
          align="center"
          width="200"
          fixed="right"
        >
          <template v-slot="{ row }">
            <span v-auth="['setting-system_menus-add']">
              <a @click="addE(row, '添加子菜单')" v-if="row.auth_type === 1"
                >添加子菜单</a
              >
              <a @click="addE(row, '添加规则')" v-else>添加规则</a>
            </span>
            <Divider type="vertical" />
            <a @click="edit(row, '编辑')">编辑</a>
            <Divider type="vertical" />
            <a @click="del(row, '删除规则')">删除</a>
          </template>
        </vxe-table-column>
      </vxe-table>
    </Card>
    <menus-from
      :formValidate="formValidate"
      :titleFrom="titleFrom"
      @getList="getList"
      @selectRule="selectRule"
      ref="menusFrom"
      @clearFrom="clearFrom"
    ></menus-from>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  getTable,
  menusDetailsApi,
  isShowApi,
  editMenus,
} from "@/api/systemMenus";
import formCreate from "@form-create/iview";
import menusFrom from "./components/menusFrom";
export default {
  name: "systemMenus",
  data() {
    return {
      spinShow: false,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      roleData: {
        is_show: "",
        keyword: "",
      },
      loading: false,
      tableData: [],
      FromData: null,
      icons: "",
      formValidate: {},
      titleFrom: "",
      modalTitleSs: "",
    };
  },
  components: { menusFrom, formCreate: formCreate.$form() },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  mounted() {
    // this.formValidate.auth_type = '1';
    this.getData();
  },
  methods: {
    // 修改规则状态
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        is_show: row.is_show,
      };
      isShowApi(data)
        .then(async (res) => {
          this.$Message.success(res.msg);
          this.$store.dispatch("admin/menus/getMenusNavList");
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 请求列表
    getList() {
      this.formValidate = Object.assign({}, this.$options.data().formValidate);
      this.getData();
    },
    selectRule(data) {
      this.formValidate.menu_name = data.real_name;
      this.formValidate.methods = data.method;
      this.formValidate.api_url = data.rule;
    },
    // 清除表单数据
    clearFrom() {
      this.formValidate = Object.assign({}, this.$options.data().formValidate);
    },
    // 添加子菜单
    addE(row, title) {
      this.formValidate = {};

      let pid = row.id.toString();
      if (pid) {
        menusDetailsApi(row.id, 2)
          .then(async (res) => {
            this.formValidate.path = res.data.path;
            this.formValidate.path.push(row.id);
            this.formValidate.pid = pid;
            this.$refs.menusFrom.modals = true;
            this.$refs.menusFrom.valids = false;
            this.titleFrom = title;
            this.formValidate.auth_type = 1;
            this.formValidate.is_show = "0";
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
      } else {
        this.formValidate.pid = pid;
        this.$refs.menusFrom.modals = true;
        this.$refs.menusFrom.valids = false;
        this.titleFrom = title;
        this.formValidate.auth_type = 1;
        this.formValidate.is_show = "0";
      }
      // this.formValidate.pid = row.id.toString();
      // this.$refs.menusFrom.modals = true;
      // this.$refs.menusFrom.valids = false;
      // this.titleFrom = title;
      // this.formValidate.auth_type = 1;
      // this.formValidate.is_show = '0';
    },
    // 删除
    del(row, tit) {
      let delfromData = {
        title: tit,
        url: `/setting/menus/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getData();
          this.$store.dispatch("admin/menus/getMenusNavList");
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 规则详情
    menusDetails(id) {
      menusDetailsApi(id, 2)
        .then(async (res) => {
          this.formValidate = res.data;
          this.$refs.menusFrom.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 编辑
    edit(row, title, index) {
      this.formValidate = {};
      this.menusDetails(row.id);
      this.titleFrom = title;
      this.$refs.menusFrom.valids = false;
      this.$refs.menusFrom.getAddFrom(row.id);
    },
    // 添加
    menusAdd(title) {
      this.formValidate = {};
      this.$refs.menusFrom.modals = true;
      this.$refs.menusFrom.valids = false;
      // this.formValidate = Object.assign(this.$data, this.$options.formValidate());
      this.titleFrom = title;
      this.formValidate.auth_type = 1;
      this.formValidate.is_show = 0;
      this.formValidate.is_show_path = 0;
      this.$refs.menusFrom.getAddFrom();
    },
    // 列表
    getData() {
      this.loading = true;
      this.roleData.is_show = this.roleData.is_show || "";
      getTable(this.roleData, 2)
        .then(async (res) => {
          this.tableData = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    // 关闭按钮
    cancel() {
      this.$emit("onCancel");
    },
  },
};
</script>

<style scoped lang="stylus">

</style>
