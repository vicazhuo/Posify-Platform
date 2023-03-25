<template>
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import buildData from "@/pages/setting/shop/buildData";

    export default {
        name: "index",
        components:{fromSubmit},
        mixins:[buildData],
        data() {
            return {
                ruleValidate: {},
                rules: [],
                url: '',
                title:'微信基础设置',
                type: 'wechat'
            };
        },
    }
</script>

<style scoped>

</style>
