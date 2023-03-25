<template>
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import buildData from "@/pages/setting/shop/buildData";

    export default {
        name: "commonForm",
        components:{fromSubmit},
        mixins:[buildData],
        data() {
            return {
                ruleValidate: {},
                rules: [],
                url: '',
                title:'',
                type: ''
            };
        },
        props:{
            typeMole: {
                type: String,
                default:''
            },
        },
        watch: {
            typeMole(){
                this.rules = [];
                this.getNewFormBuildRule();
            },
        }
    }
</script>

<style scoped>

</style>
