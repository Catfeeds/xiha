# 步骤1
svn up
npm[cnpm] install -g mocha
npm[cnpm] install -g env-cmd
npm[cnpm] install

# 步骤2
编辑修改 `.env-cmdrc` 环境变量配置
编辑修改 `package.json` 文件中的scripts项目里的test条目的环境，选择自己的环境名称，保存退出。

# 步骤3
npm[cnpm] test
