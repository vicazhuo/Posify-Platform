<template>
    <FormItem :label="title" class="input-build upload-build" :class="getClassName()">
        <template v-if="multiple">
            <div class="demo-upload-list" v-for="(item,index) in valueModel">
                <img :src="item">
                <div class="demo-upload-list-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                    <Icon type="ios-trash-outline" @click.native="handleRemove(index)"></Icon>
                </div>
            </div>
        </template>

        <div class="input-error-wrapper">
            <div v-if="disabledSeleteImage"  class="ivu-upload" @click="selectImage" style="display: inline-block; width: 58px;">
                <div class="ivu-upload ivu-upload-drag">
                    <div style="width: 58px; height: 58px; line-height: 58px;">
                        <i class="ivu-icon" :class="icon ? 'ivu-icon-' + icon : 'ivu-icon-ios-image'" style="font-size: 20px;"></i>
                    </div>
                </div>
            </div>
            <div v-if="errorMessage" class="error-wrapper">{{ errorMessage }}</div>
        </div>

        <div class="demo-upload-list" v-if="valueModel && multiple === false">
            <img :src="valueModel">
            <div class="demo-upload-list-cover">
                <Icon type="ios-eye-outline" @click.native="handleView(valueModel)"></Icon>
                <Icon type="ios-trash-outline" @click.native="handleRemove(valueModel)"></Icon>
            </div>
        </div>

        <div v-if="info" class="info-wrapper">{{ info }}</div>

        <Modal title="选择图片" v-model="visible" :styles="{height:upload.height ? upload.height :'500px'}" :width="upload.width ? upload.width :'900px'" footer-hide>
            <div :style="'height:'+(upload.height ? upload.height :'500px')">
                <iframe v-if="visible" :src="upload.url" frameborder="0" width="100%" height="100%"></iframe>
            </div>
        </Modal>
        <look-image ref="lookImage" :imageUrl="imageUrl"></look-image>
    </FormItem>
</template>

<script>

    import build from "./build";
    import lookImage from "./lookImage";

    //单选和多选iFrame图片组件
    export default {
        name: "uploadFrameBuild",
        mixins:[build],
        components:{lookImage},
        props:{
            //上传配置
            upload:{
                type: Object,
                default() {
                    return {
                        url:'',
                        width: '800px',
                        height: '900px',
                        field:'',
                        maxNum: 1
                    };
                }
            },
            icon: {
                type: String,
                default:''
            },
        },
        data(){
            return {
                visible: false,
                multiple: false,
                imageUrl:'',
                disabledSeleteImage: true,
                baseUrl:'',
            };
        },
        watch: {
            valueModel: {
                handler(n) {
                    if (this.multiple) {
                        this.getUploadUrl();
                        if (n.length >= this.upload.maxNum) {
                            this.disabledSeleteImage = false;
                        } else {
                            this.disabledSeleteImage = true;
                        }
                    } else {
                        this.disabledSeleteImage = !!!n;
                    }
                    this.changeValue();
                },
                deep: true
            },
        },
        created() {
            this.multiple = this.upload.maxNum > 1;
            this.baseUrl = this.upload.url;
            this.getUploadUrl();
            if (this.multiple) {
                if (typeof this.valueModel !== 'object') {
                    this.valueModel = [];
                }
            } else {
                if (typeof this.valueModel !== 'string') {
                    this.valueModel = '';
                }
            }
            if (this.multiple) {
                if (this.valueModel.length >= this.upload.maxNum) {
                    this.disabledSeleteImage = false;
                } else {
                    this.disabledSeleteImage = true;
                }
            } else {
                this.disabledSeleteImage = !!!this.valueModel;
            }
        },
        methods:{
            getUploadUrl() {
                let maxLength = this.multiple ? this.upload.maxNum - this.valueModel.length : 1;
                this.upload.url = this.baseUrl + (this.baseUrl.indexOf('?') !== -1 ? '&fodder='+this.field :'?fodder='+this.field) + '&maxLength=' + maxLength;
                this.upload.url += this.multiple ? '&type=many' :  'type=0';
            },
            handleView(url) {
                this.imageUrl = url;
                this.$refs.lookImage.open();
            },
            handleRemove(index) {
                if (this.multiple) {
                    this.valueModel.splice(index,1)
                } else {
                    this.valueModel = '';
                }
            },
            changeValue(){
                this.$emit('changeValue',{field:this.field,value:this.valueModel});

                //触发change事件
                this.on['change'] && this.on['change'](this.valueModel);
            },
            selectImage(){
                this.visible = true;
            },
            closeUploadFrame(){
                this.visible = false;
            },
            setValue(url){
                if (!this.multiple) {
                    this.valueModel = url[this.upload.field] || '';
                } else {
                    if (typeof url === "object") {
                        this.valueModel = url;
                    }
                }
            },
        },
        mounted() {
            if (window.$fromSubmit === undefined) {
                window.$fromSubmit = {};
            }
            window.$fromSubmit[this.field] = {
                closeUploadFrame : null,
                setValue:null,
                getValue:null,
            };
            window.$fromSubmit[this.field].closeUploadFrame = ()=> this.closeUploadFrame();
            window.$fromSubmit[this.field].getValue = ()=> this.valueModel;
            window.$fromSubmit[this.field].setValue = (url)=> this.setValue(url);
        }

    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
