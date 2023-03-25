<template>
  <div>
    <Modal v-model="modals" width="700" scrollable footer-hide closable :title="titleFrom" :z-index="1"
      @on-cancel="handleReset" class-name="vertical-center-modal">
      <Form ref="formValidate" :model="formValidate" :label-width="110" :rules="ruleValidate" @submit.native.prevent>
        <Col v-bind="grid">
        <FormItem label="上级品牌：">
          <Cascader :data="FromData" placeholder="请选择上级品牌" change-on-select v-model="formValidate.fid"></Cascader>
        </FormItem>
        </Col>
        <Col v-bind="grid">
        <FormItem label="品牌名称：" prop="brand_name">
          <Input v-model="formValidate.brand_name" maxlength='10' placeholder="请输入品牌名称" prop=""></Input>
        </FormItem>
        </Col>
        <Col v-bind="grid">
        <FormItem label="品牌排序：">
          <!-- <Input
                    v-model="formValidate.sort"
                    placeholder="请输入分类排序"
                    prop=""
            ></Input> -->
          <InputNumber v-model="formValidate.sort" :step="1" placeholder="请输入品牌排序" style="width: 100%;"></InputNumber>
        </FormItem>
        </Col>
        <Col v-bind="grid">
        <FormItem label="是否显示：">
          <i-switch v-model="formValidate.is_show" size="large" :true-value="1" :false-value="0">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </i-switch>
        </FormItem>
        </Col>
        <Col span="24">
        <div style="display:flex;justify-content: flex-end;">
        <Button style="margin-right:14px" type="primary" @click="cancle">取消</Button>
        <Button type="default" @click="handleSubmit('formValidate')">确认</Button>
        </div>
        </Col>
      </Form>
    </Modal>

  </div>
</template>

<script>
import { brandCascader, productBrand, productBrandrev } from "@/api/product";

export default {
  name: "menusFrom",
  props: {
    formValidate: {
      type: Object,
      default: null,
    },
    fromName: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      ruleValidate: {
        brand_name: [
          { required: true, message: '请输入品牌名称', trigger: 'blur' }
        ],
      },
      type: 1,
      modals: false,
      // authType:false,
      FromData: [],
      titleFrom: '',
      grid: {
        xl: 24,
        lg: 24,
        md: 12,
        sm: 24,
        xs: 24,
      },
    }
  },
  mounted() {
    this.getAddFrom()
  },
  methods: {
    // 获取新增表单
    getAddFrom() {
      brandCascader().then(async (res) => {
        this.FromData = res.data;
      })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    handleReset() {
      this.modals = false;
      this.$parent.getData()
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (this.type == 2) {
            productBrandrev(this.formValidate.id, this.formValidate).then(res => {
              this.$Message.success(res.msg)
              this.getAddFrom()
              this.$parent.getData()
              this.modals = false
            }).catch(err => {
              this.$Message.error(err.msg)
            })
          } else {
            productBrand(this.formValidate).then(res => {
              this.$Message.success(res.msg)
              this.getAddFrom()
              if (this.fromName) {
                this.$parent.getBrandList();
              } else {
                this.$parent.getData()
              }
              this.modals = false
            }).catch(err => {
              this.$Message.error(err.msg)
            })
          }
        } else {
          this.$Message.error('请输入品牌名称');
        }
      })

    },
    cancle() {
      this.modals = false
    }
  }
};
</script>

<style scoped lang="stylus">
  /deep/.ivu-cascader-menu{
    height: auto;
    max-height 165px !important
  }

  /deep/.ivu-cascader-menu-item{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
    /deep/.ivu-select-item{
      max-width 294px
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
.trees-coadd {
  width: 100%;
  height: 500px;
  border-radius: 4px;
  overflow: hidden;
}
.scollhide {
  width: 100%;
  height: 100%;
  overflow: auto;
  margin-left: 18px;
  padding: 10px 0 10px 0;
  box-sizing: border-box;
}
.content {
  font-size: 12px;
}

.time {
  font-size: 12px;
  color: #2d8cf0;
}

.icons-item {
  float: left;
  margin: 6px 6px 6px 0;
  width: 53px;
  text-align: center;
  list-style: none;
  cursor: pointer;
  height: 50px;
  color: #5c6b77;
  transition: all 0.2s ease;
  position: relative;
  padding-top: 10px;
	font-size: 20px;
}
.search-rule {
  display: flex;
  align-items: center;
  padding: 10px;
  background-color: #f2f2f2;
}
.rule {
  display: flex;
  flex-wrap: wrap;
  max-height: 700px;
  overflow: scroll;
}
/*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
.rule::-webkit-scrollbar {
  width: 6px;
  height: 6px;
  background-color: #f5f5f5;
}

/*定义滚动条轨道 内阴影+圆角*/
.rule::-webkit-scrollbar-track {
  border-radius: 4px;
  background-color: #f5f5f5;
}

/*定义滑块 内阴影+圆角*/
.rule::-webkit-scrollbar-thumb {
  border-radius: 4px;
  background-color: #555;
}
.rule-list {
  background-color: #f2f2f2;
  width: 32%;
  margin: 5px;
  border-radius: 3px;
  padding: 10px;
  color: #333;
  cursor: pointer;
  transition: all 0.1s;
  overflow: hidden;
}
.rule-list:hover {
  background-color: #c5d1dd;
}
.rule-list div {
  white-space: nowrap;
}
.select-rule {
  background-color: #c5d1dd;
}
.add {
  display: flex;
  align-items: center;
}
</style>
