<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="brandData"
          inline
          :model="brandData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="品牌搜索：">
            <Input
              v-model="brandData.keyword"
              placeholder="请输入品牌名称"
              style="width: 250px;margin-right:14px;"
            ></Input>
            <Button type="primary" @click="search">查询</Button>
          </FormItem>
        </Form>
      </div>
      <!-- <div class="seach">品牌搜索：</div>
      <Input
        v-model="brandData.keyword"
        placeholder="请输入品牌名称"
        style="width: 250px;margin-right:14px;"
      />
      <Button type="primary" @click="search">查询</Button> -->
    </Card>

    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button type="primary" @click="menusAdd">添加品牌</Button>
      <vxe-table
        :border="false"
        class="vxeTable mt25"
        highlight-hover-row
        highlight-current-row
        :loading="loading"
        ref="xTable"
        header-row-class-name="false"
        row-id="id"
        :tree-config="{lazy: true, children: 'children', hasChild: 'children',loadMethod: loadChildrenMethod, reserve: true }"
        :data="tableData"
      >
        <vxe-table-column
          field="brand_name"
          tree-node
          title="品牌名称"
          min-width="240"
        ></vxe-table-column>
        <vxe-table-column
          field="brand_num"
          title="使用商品"
          min-width="120"
          tooltip="true"
        ></vxe-table-column>
        <vxe-table-column field="flag" title="状态" min-width="120">
          <template v-slot="{ row }">
            <i-switch
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
          field="sort"
          title="排序"
          min-width="120"
        ></vxe-table-column>
        <vxe-table-column
          field="add_time"
          title="创建时间"
          min-width="180"
          tooltip="true"
        ></vxe-table-column>

        <vxe-table-column
          field="date"
          title="操作"
          align="center"
          width="200"
          fixed="right"
        >
          <template v-slot="{ row }">
            <span v-auth="['setting-system_menus-add']">
              <a @click="addE(row)" v-if="row.type === 1">添加子品牌</a>
            </span>
            <Divider v-if="row.type === 1" type="vertical" />
            <a @click="edit(row, '编辑')">编辑</a>
            <Divider type="vertical" />
            <a @click="del(row, '删除该品牌')">删除</a>
          </template>
        </vxe-table-column>
      </vxe-table>
    </Card>
    <menus-from :formValidate="formValidate" ref="menusFrom"></menus-from>
  </div>
</template>

<script>
import menusFrom from "./components/menusFrom";
import { mapState } from "vuex";
import { productBrandlist, productBrandShow } from "@/api/product";
export default {
  name: "index",
  components: {
    menusFrom,
  },
  data() {
    return {
      grid: {
        xl: 12,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24,
      },
      grids: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      brandData: {
        keyword: "",
      },
      loading: false,
      tableData: [],
      formValidate: {},
      titleFrom: "",
    };
  },
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
    this.getData();
  },
  methods: {
    search() {
      this.getData();
    },
    getData() {
      this.loading = true;
      productBrandlist({ brand_name: this.brandData.keyword }).then((res) => {
        this.loading = false;
        this.tableData = res.data.list;
      });
    },
		loadChildrenMethod ({row}){
			  return new Promise((resolve, reject) => {
					productBrandlist({ brand_name: this.brandData.keyword,pid:row.id }).then(res=>{
						let arr = res.data.list;
						resolve(arr);
					})
				})
		},
    menusAdd() {
      this.$refs.menusFrom.modals = true;
      this.$refs.menusFrom.titleFrom = "添加品牌分类";
      this.formValidate = {
        sort: 0,
      };
      this.formValidate.is_show = 1;
      this.$refs.menusFrom.type = 1;
    },
    // 修改状态
    onchangeIsShow(row) {
      productBrandShow(row.id, row.is_show)
        .then((res) => {
          this.getData();
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.getData();
          this.$Message.error(err.msg);
        });
    },
    // 添加子品牌
    addE(row) {
      this.$refs.menusFrom.modals = true;
      this.$refs.menusFrom.titleFrom = "添加品牌分类";
      this.formValidate = {
        sort: 0,
      };
      this.formValidate.fid = row.fid_son;
      this.formValidate.is_show = 1;
      this.$refs.menusFrom.type = 3;
    },
    // 编辑
    edit(row) {
      this.$refs.menusFrom.modals = true;
      this.$refs.menusFrom.titleFrom = "编辑品牌分类";
      this.formValidate = row;
      if (row.fid.length == 0) {
        this.formValidate.fid = [0];
      }
      this.$refs.menusFrom.type = 2;
    },
    // 删除
    del(row, tit) {
      let delfromData = {
        title: tit,
        url: `product/brand/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.$refs.menusFrom.getAddFrom();
          this.getData();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>

<style scoped lang="stylus">
.btw {
  justify-content: space-between;
}

.seach {
  float: left;
  line-height: 30px;
}
</style>