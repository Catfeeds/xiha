<style scoped lang="less">
    .index{
        width: 100%;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        text-align: center;
        h1{
            height: 150px;
            img{
                height: 100%;
            }
        }
        h2{
            color: #666;
        }
        .ivu-row{
            height: 100%;
        }
    }
</style>
<template>
    <div class="index">
        <Row type="flex" justify="center" align="middle">
            <i-col span="24">
                <h1>
                    <img src="../images/logo.png">
                </h1>
                <h2>一套基于 Vue.js 的高质量UI组件库</h2>
            </i-col>
        </Row>

        <Switch>
            <span slot="open">开</span>
            <span slot="close">关</span>
        </Switch>
        <Switch>
            <Icon type="android-done" slot="open"></Icon>
            <Icon type="android-close" slot="close"></Icon>
        </Switch>
        <br><br>
        <Switch size="large">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
        </Switch>
        <Switch size="large">
            <span slot="open">ON</span>
            <span slot="close">OFF</span>
        </Switch>

        <i-button type="primary" @click="modal1 = true">显示对话框</i-button>
        <Modal
            :visible.sync="modal1"
            title="普通的Modal对话框标题"
            @on-ok="ok"
            @on-cancel="cancel">
            <p>对话框内容</p>
            <p>对话框内容</p>
            <p>对话框内容</p>
        </Modal>

        <i-button type="primary" @click="open(false)">打开提醒</i-button>
        <i-button @click="open(true)">打开提醒（仅标题）</i-button>

        <p>带描述信息</p>
        <i-button @click="info(false)">消息</i-button>
        <i-button @click="success(false)">成功</i-button>
        <i-button @click="warning(false)">警告</i-button>
        <i-button @click="error(false)">错误</i-button>
        <p>仅标题</p>
        <i-button @click="info(true)">消息</i-button>
        <i-button @click="success(true)">成功</i-button>
        <i-button @click="warning(true)">警告</i-button>
        <i-button @click="error(true)">错误</i-button>

        <i-select :model.sync="model1" style="width:200px">
            <i-option v-for="item in cityList" :value="item.value">{{ item.label }}</i-option>
        </i-select>

        <i-button>Default</i-button>
        <i-button type="primary">Primary</i-button>
        <i-button type="ghost">Ghost</i-button>
        <i-button type="dashed">Dashed</i-button>
        <i-button type="text">Text</i-button>
        <br><br>
        <i-button type="info">信息按钮</i-button>
        <i-button type="success">成功按钮</i-button>
        <i-button type="warning">警告按钮</i-button>
        <i-button type="error">错误按钮</i-button>
        <br>
        <Icon type="checkmark"></Icon>
        <Icon type="arrow-down-a"></Icon>
        <Icon type="minus-circled"></Icon>

        <i-input :value.sync="input_value" placeholder="请输入..." style="width: 300px"></i-input>

        <Alert>消息提示文案</Alert>
        <Alert type="success">成功提示文案</Alert>
        <Alert type="warning">警告提示文案</Alert>
        <Alert type="error">错误提示文案</Alert>

        <i-button type="primary" :loading="loading2" icon="checkmark-round" @click="toLoading2">
            <span v-if="!loading2">Click me!</span>
            <span v-else>Loading...</span>
        </i-button>
        <i-button @click="start">Start</i-button>
        <i-button @click="finish">Finish</i-button>
        <i-button @click="loading_error">Error</i-button>

        <Collapse accordion>
          <Panel>
              史蒂夫·乔布斯
              <div slot="content">
                  史蒂夫·乔布斯（Steve Jobs），1955年2月24日生于美国加利福尼亚州旧金山，美国发明家、企业家、美国苹果公司联合创办人。
                  <Collapse accordion>
                      <Panel>
                          iPhone
                          <p slot="content">iPhone，是美国苹果公司研发的智能手机，它搭载iOS操作系统。第一代iPhone于2007年1月9日由苹果公司前首席执行官史蒂夫·乔布斯发布，并在2007年6月29日正式发售。</p>
                      </Panel>
                      <Panel>
                          iPad
                          <p slot="content">iPad是由苹果公司于2010年开始发布的平板电脑系列，定位介于苹果的智能手机iPhone和笔记本电脑产品之间，（屏幕中有4个虚拟程序固定栏）与iPhone布局一样，提供浏览网站、收发电子邮件、观看电子书、播放音频或视频、玩游戏等功能。由英国出生的设计主管乔纳森·伊夫（Jonathan Ive）（有些翻译为 乔纳森·艾维）领导的团队设计的，这个圆滑、超薄的产品反映出了伊夫对德国天才设计师Dieter Rams的崇敬之情。</p>
                      </Panel>
                  </Collapse>
              </div>
          </Panel>
          <Panel>
              斯蒂夫·盖瑞·沃兹尼亚克
              <p slot="content">斯蒂夫·盖瑞·沃兹尼亚克（Stephen Gary Wozniak），美国电脑工程师，曾与史蒂夫·乔布斯合伙创立苹果电脑（今之苹果公司）。斯蒂夫·盖瑞·沃兹尼亚克曾就读于美国科罗拉多大学，后转学入美国著名高等学府加州大学伯克利分校（UC Berkeley）并获得电机工程及计算机（EECS）本科学位（1987年）。</p>
          </Panel>
          <Panel>
              乔纳森·伊夫
              <p slot="content">乔纳森·伊夫是一位工业设计师，现任Apple公司设计师兼资深副总裁，英国爵士。他曾参与设计了iPod，iMac，iPhone，iPad等众多苹果产品。除了乔布斯，他是对苹果那些著名的产品最有影响力的人。</p>
          </Panel>
      </Collapse>

      <Card style="width:350px">
          <p slot="title">
              <Icon type="ios-film-outline"></Icon>
              经典电影
          </p>
          <a href="#" slot="extra" @click.prevent="changeLimit">
              <Icon type="ios-loop-strong"></Icon>
              换一换
          </a>

          <ul>
              <li v-for="item in movieList | limitBy limitNum limitFrom">
                  <a :href="item.url" target="_blank">{{ item.name }}</a>
                  <span>
                      <Icon type="ios-star" v-for="n in 4"></Icon><Icon type="ios-star" v-if="item.rate >= 9.5"></Icon><Icon type="ios-star-half" v-else></Icon>
                      {{ item.rate }}
                  </span>
              </li>
          </ul>
      </Card>

      <i-button type="info" @click="messageInfo">显示普通提醒</i-button>
      <i-button type="success" @click="messageSucess">显示成功提醒</i-button>
      <i-button type="error" @click="messageError">显示错误提醒</i-button>
      <i-button type="warning" @click="messageWarning">显示警告提醒</i-button>

      <Checkbox-group :model.sync="fruit">
          <Checkbox value="香蕉"></Checkbox>
          <Checkbox value="苹果"></Checkbox>
          <Checkbox value="西瓜"></Checkbox>
      </Checkbox-group>

    </div>
    
</template>

<script>
    export default {
        data () {
            return {
                limitNum: 5,
                limitFrom: 0,
                movieList: [
                    {
                        name: '肖申克的救赎',
                        url: 'https://movie.douban.com/subject/1292052/',
                        rate: 9.6
                    },
                    {
                        name: '这个杀手不太冷',
                        url: 'https://movie.douban.com/subject/1295644/',
                        rate: 9.4
                    },
                    {
                        name: '霸王别姬',
                        url: 'https://movie.douban.com/subject/1291546/',
                        rate: 9.5
                    },
                    {
                        name: '阿甘正传',
                        url: 'https://movie.douban.com/subject/1292720/',
                        rate: 9.4
                    },
                    {
                        name: '美丽人生',
                        url: 'https://movie.douban.com/subject/1292063/',
                        rate: 9.5
                    },
                    {
                        name: '千与千寻',
                        url: 'https://movie.douban.com/subject/1291561/',
                        rate: 9.2
                    },
                    {
                        name: '辛德勒的名单',
                        url: 'https://movie.douban.com/subject/1295124/',
                        rate: 9.4
                    },
                    {
                        name: '海上钢琴师',
                        url: 'https://movie.douban.com/subject/1292001/',
                        rate: 9.2
                    },
                    {
                        name: '机器人总动员',
                        url: 'https://movie.douban.com/subject/2131459/',
                        rate: 9.3
                    },
                    {
                        name: '盗梦空间',
                        url: 'https://movie.douban.com/subject/3541415/',
                        rate: 9.2
                    }
                ],
                input_value: 'chenxi',
                cityList: [
                    {
                        value: 'beijing',
                        label: '北京市'
                    },
                    {
                        value: 'shanghai',
                        label: '上海市'
                    },
                    {
                        value: 'shenzhen',
                        label: '深圳市'
                    },
                    {
                        value: 'hangzhou',
                        label: '杭州市'
                    },
                    {
                        value: 'nanjing',
                        label: '南京市'
                    },
                    {
                        value: 'chongqing',
                        label: '重庆市'
                    }
                ],
                loading2: false,
                fruit: ['苹果'],
                modal1: false,
            }
        },
        methods: {
            ok () {
                this.$Message.info('点击了确定');
            },
            cancel () {
                this.$Message.info('点击了取消');
            },
            open (nodesc) {
                this.$Notice.open({
                    title: '这是通知标题',
                    desc: nodesc ? '' : '这里是通知描述这里,是通知描述这里是通知描述这里,是通知描述这里,是通知描述这里是通知描述这里是通知描述'
                });
            },
            info (nodesc) {
                this.$Notice.info({
                    title: '这是通知标题',
                    desc: nodesc ? '' : '这里是通知描述这里,是通知描述这里是通知描述这里,是通知描述这里,是通知描述这里是通知描述这里是通知描述'
                });
            },
            success (nodesc) {
                this.$Notice.success({
                    title: '这是通知标题',
                    desc: nodesc ? '' : '这里是通知描述这里,是通知描述这里是通知描述这里,是通知描述这里,是通知描述这里是通知描述这里是通知描述'
                });
            },
            warning (nodesc) {
                this.$Notice.warning({
                    title: '这是通知标题',
                    desc: nodesc ? '' : '这里是通知描述这里,是通知描述这里是通知描述这里,是通知描述这里,是通知描述这里是通知描述这里是通知描述'
                });
            },
            error (nodesc) {
                this.$Notice.error({
                    title: '这是通知标题',
                    desc: nodesc ? '' : '这里是通知描述这里,是通知描述这里是通知描述这里,是通知描述这里,是通知描述这里是通知描述这里是通知描述'
                });
            },
            toLoading2 () {
                this.loading2 = true;
            },
            start () {
                this.$Loading.start();
            },
            finish () {
                this.$Loading.finish();
            },
            loading_error () {
                this.$Loading.error();
            },
            changeLimit () {
                this.limitFrom = this.limitFrom === 0 ? 5 : 0;
            },
            messageInfo () {
              this.$Message.info('这是一条普通消息');
            },
            messageSucess () {
              this.$Message.success('这是一条成功消息');
            },
            messageError () {
              this.$Message.error('这是一条错误消息');
            },
            messageWarning () {
              this.$Message.warning('这是一条警告消息');
            },

        }
    }
</script>
