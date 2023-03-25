<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div class="acea-row row-between-wrapper mb20">
        <Row type="flex">
          <Col v-bind="grid">
            <div class="button acea-row row-middle">
              <Button class="mr20" type="primary" @click="add(0)"
                >添加城市</Button
              >
              <!-- <Button type="primary" @click="cleanCache">清除缓存</Button> -->
            </div>
          </Col>
        </Row>
      </div>
			<Table row-key="id" :load-data="handleLoadData" :columns="columns1" :data="cityLists" update-show-children>
				<template slot-scope="{ row, index }" slot="action">
					<a v-if="row.hasOwnProperty('level') && row.level < 4" @click="add(row.id)">添加</a>
					<Divider v-if="row.hasOwnProperty('level') && row.level < 4" type="vertical" />
					<a @click="edit(row.id)">编辑</a>
					<Divider v-if="!row.hasOwnProperty('children')" type="vertical" />
					<a v-if="!row.hasOwnProperty('children')" @click="del(row, '删除城市', index)">删除</a>
				</template>
			</Table>
    </Card>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  cityListApi,
  cityAddApi,
  cityApi,
  cityCleanCacheApi,
} from "@/api/setting";
export default {
  name: "setting_dada",
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      columns1: [
        {
          title: "编号",
          key: "id",
          width: 80,
        },
        {
          title: "地区名称",
          key: "label",
          minWidth: 300,
					tree: true
        },
				{
				  title: "上级名称",
				  key: "parent_name",
				  minWidth: 300,
				},
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 120,
        },
      ],
      cityLists: [],
      cityId: 0, // 城市id
    };
  },
  computed: {
    ...mapState("media", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 75;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  created() {
    this.getList(0);
  },
  methods: {
		// 城市列表
		getList(parentId) {
		  let that = this;
		  that.loading = true;
		  cityListApi(parentId)
		    .then((res) => {
		      that.cityLists = res.data;
		      that.loading = false;
		    })
		    .catch((res) => {
		      that.loading = false;
		      that.$Message.error(res.msg);
		    });
		},
		// handleLoadData (item, callback) {
		// 	cityListApi(item.id).then(res=>{
		// 		callback(res.data);
		// 	});
		// },
		handleLoadData (item, callback) {
			item._loading = true;
			cityListApi(item.id).then(res=>{
				// item.children = res.data;
				item._loading = false;
				callback(res.data);  
			});
		},
		// 删除
		del(row, tit, num) {
		  let delfromData = {
		    title: tit,
		    num: num,
		    url: `setting/city/del/${row.id}`,
		    method: "DELETE",
		    ids: "",
		  };
		  this.$modalSure(delfromData)
		    .then((res) => {
		      this.$Message.success(res.msg);
		      this.cityLists.splice(num, 1);
		      this.getList(0);
		    })
		    .catch((res) => {
		      this.$Message.error(res.msg);
		    });
		},
		// 修改
		edit(id) {
		  this.$modalForm(cityApi(id)).then(() => this.getList(0));
		},
		// 添加
		add(cityId) {
		  this.$modalForm(cityAddApi(cityId)).then(() => this.getList(0));
		},	
    // 清除缓存；
    cleanCache() {
      cityCleanCacheApi()
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((res) => {
          this.$Message.success(res.msg);
        });
    } 
  },
};
</script>

<style scoped lang="stylus">
/deep/.ivu-table-cell-tree{
	border:0;
	font-size 15px;
	background-color unset;
}	
/deep/.ivu-table-cell-tree .ivu-icon-ios-add:before{
	content: "\F11F";
}
/deep/.ivu-table-cell-tree .ivu-icon-ios-remove:before{
	content: "\F116";
}	
.button{
	width 300px;
}   
</style>
