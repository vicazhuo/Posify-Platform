<template>
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import buildData from "./buildData";

    export default {
        name: "pay",
        components:{fromSubmit},
        mixins:[buildData],
        data() {
            return {
                ruleValidate: {},
                rules: [],
                url: '',
                title:'支付设置',
                type: 'pay'
            };
        },
    }
</script>

<style scoped>

</style>
