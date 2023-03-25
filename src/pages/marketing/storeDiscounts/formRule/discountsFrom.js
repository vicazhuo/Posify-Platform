const discountsFrom = {
  title: [{ required: true, message: "请输入套餐名称", trigger: "blur" }],
  type: [
    { required: true, type: 'number', message: "请选择套餐类型", trigger: "change" },
  ],
  time: [{ required: true, validator: validate, trigger: 'change' }],
  limit_num: [
    { required: true, type: "number", message: "请输入套餐数量", trigger: "blur" },
  ],
  image: [
    {
      required: true,
      message: "请上传套餐主图",
      trigger: 'change'
    },
  ],
}
function validate(rule, value, callback) {
  console.log(value)
  if (Array.isArray(value)) { //格式为：daterange、datetimerange检测
    value.map(function (item) {
      if (item === "") {
        return callback("日期不能为空")
      }
    })
  } else { //格式为：date、datetime、year、month 检测
    if (value === '') {
      return callback("日期不能为空")
    }
  }
  return callback()
}

export { discountsFrom }
