<template>
    <Modal :title="title" v-model="visible" footer-hide>
				<div class="image">
					<img :src="imageUrl" v-if="visible" :style="styleImg ? styleImg : 'width: 100%;'">
				</div>
        <div slot="footer">
        </div>
    </Modal>
</template>

<script>
    export default {
        name: "lookImage",
        props:{
            imageUrl: {
                type: String,
                default:''
            },
            styleImg: {
                type: String,
                default:''
            },
            title: {
                type: String,
                default:'查看图片'
            },
        },
        data() {
            return {
                visible: false,
            };
        },
        methods: {
            clear() {
                this.visible = false;
            },
            open() {
                this.visible = true;
            },
        },
    }
</script>

<style scoped>
	.image{
		background: url('../../assets/images/transparents.jpg') no-repeat;
		background-size: 100% 100%;
	}
</style>
