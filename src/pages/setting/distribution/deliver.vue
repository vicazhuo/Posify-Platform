<template>
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import buildData from "@/pages/setting/shop/buildData";

    export default {
        name: "deliver",
        components: {fromSubmit},
        mixins: [buildData],
        data() {
            return {
                ruleValidate: {},
                rules: [],
                url: '',
                title: '发货设置',
                type: 'deliver'
            };
        },
    }
</script>

<style scoped>

</style>
