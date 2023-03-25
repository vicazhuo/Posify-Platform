import {getNewFormBuildRuleApi} from '@/api/setting'

export default {
    mounted() {
        this.getNewFormBuildRule()
    },
    methods: {
        getNewFormBuildRule() {
            getNewFormBuildRuleApi(this.type ? this.type : this.typeMole).then(res => {
                this.rules = res.data.rules;
                this.url = res.data.url;
                let validate = res.data.validate;
                Object.keys(validate).map(key => {
                    if (typeof validate[key] === "object") {
                        validate[key].map(item => {
                            if (item.pattern !== undefined) {
                                item.pattern = eval(item.pattern);
                            }
                        })
                    }
                });
                this.ruleValidate = validate
            })
        },
        setRulesValue(rules, data) {
            rules.map(item => {
                if (item.field !== undefined) {
                    item.value = data[item.field] || '';
                }
                if (typeof item.options === 'object') {
                    item.options.map(option => {
                        if (option.componentsModel !== undefined) {
                            option.componentsModel = this.setRulesValue(option.componentsModel, data);
                        }
                    });
                }
                if (typeof item.control === 'object') {
                    item.control.map(value => {
                        if (value.componentsModel !== undefined) {
                            value.componentsModel = this.setRulesValue(value.componentsModel, data);
                        }
                    });
                }
                if (typeof item.componentsModel === "object") {
                    item.componentsModel = this.setRulesValue(item.componentsModel, data);
                }
            });
            return rules;
        }
    },
}
