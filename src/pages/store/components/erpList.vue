<template>
	<div>
		<Table :columns="columns1" :data="list" ref="table" class="mt25"
		       :loading="loading" highlight-row
		       no-userFrom-text="暂无数据"
		       no-filtered-userFrom-text="暂无筛选结果">
		</Table>
		<div class="acea-row row-right page">
			<Button icon="ios-arrow-back" :disabled="disabledB" @click="back"></Button>
			<Button icon="ios-arrow-forward" :disabled="disabledF" class="ml10" @click="forward"></Button>
		</div>
	</div>
</template>

<script>
    import { mapState } from 'vuex';
		import { erpShop } from "@/api/erp";
    export default {
        name: "erpList",
        data() {
            return {
                loading: false,
				currentid: 0,
                columns1: [
					{
						width: 60,
						align: "center",
						render: (h, params) => {
							let id = params.row.shop_id;
							let flag = false;
							if (this.currentid === id) {
								flag = true;
							} else {
								flag = false;
							}
							let self = this;
							return h("div", [
								h("Radio", {
									props: {
										value: flag,
										disabled:id?false:true
									},
									on: {
										"on-change": () => {
											self.currentid = id;
											this.$emit("getProductId",id);
										},
									},
								}),
							]);
						},
					},
                    {
                        title: 'ID',
                        key: 'shop_id',
                        width: 120
                    },
                    {
                        title: '店铺名称',
                        key: 'shop_name',
                        minWidth: 100
                    }
                ],
                erpFrom: {
                    page: 1,
                    limit: 1
                },
                list: [],
				disabledB:true,
				disabledF:false
            }
        },
        computed: {
            ...mapState('admin/layout', [
                'isMobile'
            ]),
            labelWidth () {
                return this.isMobile ? undefined : 96;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        created () {
            this.getList();
        },
        methods:{
			back(){
				this.erpFrom.page = this.erpFrom.page - 1;
				this.getList()
			},
			forward(){
				this.erpFrom.page = this.erpFrom.page + 1;
				this.getList()
			},
            // erp门店列表
            getList () {
                this.loading = true;
                erpShop(this.erpFrom).then(res => {
					let data = res.data;
                    this.list = data;
					if(this.erpFrom.page > 1){
						this.disabledB = false
					}else {
						this.disabledB = true
					}
					if(data.length<this.erpFrom.limit){
						this.disabledF = true
					}else {
						this.disabledF = false
					}
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                    this.$Message.error(err.msg);
                })
            },
            pageChange (index) {
                this.specsFrom.page = index;
                this.getList();
            }
        }
    }
</script>

<style>
</style>
