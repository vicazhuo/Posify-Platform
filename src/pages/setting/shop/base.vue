<template>
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import buildData from "./buildData";

    export default {
        name: "base-index",
        components:{fromSubmit},
        mixins:[buildData],
        data() {
            return {
                ruleValidate: {
                    site_name: [
                        { required: true, message: '请输入网站名称', trigger: 'blur' }
                    ],
                    site_url: [
                        { required: true, message: '请填写网站地址', trigger: 'blur' }
                    ]
                },
                rules: [],
                url: '',
                title:'基础设置',
                type: 'base'
            };
        },
    }
</script>

<style scoped>

</style>
