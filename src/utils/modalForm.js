// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import request from '@/plugins/request';
import Modal from './modal'
import Vue from 'vue';
import { Message, Spin, Notice } from 'iview'
let modalInstance

function getModalInstance(render = undefined) {
    modalInstance = modalInstance || Modal.newInstance({
        closable: true,
        maskClosable: true,
        footerHide: true,
        render: render
    })

    return modalInstance
}

function alert(options) {
    const render = ('render' in options) ? options.render : undefined
    let instance = getModalInstance(render)

    options.onRemove = function () {
        modalInstance = null
    }

    instance.show(options)
}

export default function (formRequestPromise, { width = '700' } = { width: '700' }) {
    return new Promise((resolve) => {
        const msg = Message.loading({
            content: 'Loading...',
            duration: 0
        });
        formRequestPromise.then(({ data }) => {
            if (data.status === false) {
                msg();
                return Notice.warning({
                    title: data.title,
                    duration: 3,
                    desc: data.info,
                    render: h => {
                        return h('div', [
                            h('a', {
                                attrs: {
                                    href: 'http://www.crmeb.com'
                                }
                            }, data.info)
                        ])
                    }
                });
            }
            data.config = {};
            data.config.global = {
                upload: {
                    props: {
                        onSuccess(res, file) {
                            if (res.status === 200) {
                                file.url = res.data.src;
                            } else {
                                Message.error(res.msg);
                            }
                        }
                    }
                },
                frame: {
                    props: {
                        closeBtn: false,
                        okBtn: false
                    }
                }
            }
            data.config.onSubmit = function (formData, $f) {
                $f.btn.loading(true);
                $f.btn.disabled(true);
                request[data.method.toLowerCase()](data.action, formData).then((res) => {
                    modalInstance.remove()
                    Message.success(res.msg || '提交成功')
                    resolve(res)
                }).catch(err => {
                    Message.error(err.msg || '提交失败')
                }).finally(() => {
                    $f.btn.loading(false)
                    $f.btn.disabled(false);
                })
            }
            data.config.submitBtn = false
            data.config.resetBtn = false
            if (!data.config.form) data.config.form = {}
            // data.config.form.labelWidth = 100
            let fApi
            data = Vue.observable(data);
            alert({
                title: data.title,
                width,
                loading: false,
                render: function (h) {
                    return h('div', { class: 'common-form-create' }, [
                        h('formCreate', {
                            props: {
                                rule: data.rules,
                                option: data.config
                            },
                            on: {
                                mounted: ($f) => {
                                    fApi = $f
                                    msg()
                                }
                            }
                        }),
                        h('div', {style:'display:flex;justify-content: flex-end;'}, [
                            h('Button', {
                                class: 'common-form-button',
                                style: 'margin-right:14px',
                                props: {
                                    type: 'default',
                                },
                                on: {
                                    click: () => {
                                        modalInstance.remove();
                                        console.log(fApi);       
                                        
                                    }
                                }
                            }, ['取消']),

                            h('Button', {
                                class: 'common-form-button',
                                props: {
                                    type: 'primary',
                                },
                                on: {
                                    click: () => {
                                        fApi.submit()
                                    }
                                }
                            }, ['确认'])
                            
                        ])
                    ])
                }
            })
        }).catch(res => {
            Spin.hide();
            msg();
            Message.error(res.msg || '表单加载失败');
        })
    })
}