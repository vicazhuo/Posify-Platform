<template>
    <div class="label-wrapper">
				<div class="list-box">
					<div class="label-box" v-for="(item,index) in attrs" :key="index">
					    <div class="title">{{item.value}}</div>
					    <div class="list">
					        <div class="label-item" :class="label.select?'on':''" v-for="(label,j) in item.details" :key="j" @click="selectAttr(label,j)">{{label.name}}</div>
					    </div>
					</div>
				</div>
        <div class="footer">
			<Button type="primary" class="btns" ghost @click="cancel">取消</Button>
			<Button type="primary" class="btns" ghost @click="reset">重置</Button>
            <Button type="primary" class="btns" @click="subBtn">确定</Button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "attrList",
        props:{
			attrs: {
			  type: Array,
			  default: function () {
			    return [];
			  }
			}
        },
        data(){
            return {
            }
        },
        mounted() {
        },
        methods:{
			selectAttr(label,index){
				label.select = !label.select;
				this.$emit('activeData',JSON.parse(JSON.stringify(this.attrs)))
			},
			cancel(){
				this.$emit('close')
			},
			reset(){
				let data = this.attrs;
				data.map(el=>{
					el.details.map(label=>{
						label.select = false
					})
				})
				this.attrs = data;
			},
			subBtn(){
				this.$emit('subAttrs')
			}
        }
    }
</script>
<style lang="stylus" scoped>
.label-wrapper{
	 .list{
		 display flex
		 flex-wrap wrap
		 .label-item{
			 margin 10px 8px 10px 0
			 padding: 3px 14px;
			 background #EEEEEE
			 color #333333
			 border-radius 2px
			 cursor pointer
			 font-size 12px
			 border 1px solid #EEEEEE
			 &.on{
				 color #1890FF
				 border-color #1890FF
				 background-color #fff;
			 }
		 }
	 }
	 .footer{
		 display flex
		 justify-content flex-end
		 margin-top 40px
		 button{
			  margin-left 10px
		 }
	 }
}
.btn{
	width 60px
	height 24px
}	
.title{
	font-size 13px
}
.list-box{
	overflow-y auto
	overflow-x hidden
	max-height 240px
}	
</style>
