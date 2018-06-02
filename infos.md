> ##M+TP 目录结构
>~~~
>www  WEB部署目录（或者子目录）
>├─apps                                          应用目录
>│  ├─api                                        api模块
>│  │  ├─config.php                              模块配置文件
>│  │  ├─common.php                              模块函数文件
>│  │  └─controller                              控制器目录
>│  ├─common                                     公共模块目录（可以更改）
>│  │  ├─config.php                              模块配置文件
>│  │  ├─common.php                              模块函数文件
>│  │  ├─controller                              控制器目录
>│  │  ├─model                                   模型目录
>│  │  ├─validate                                验证器目录
>│  │  └─behavior                                行为目录
>│  ├─index                                      Index模块
>│  │  ├─config.php                              模块配置文件
>│  │  ├─common.php                              模块函数文件
>│  │  └─controller                              控制器目录
>│  ├─command.php                                命令行工具配置文件
>│  ├─common.php                                 公共函数文件
>│  ├─config.php                                 公共配置文件
>│  ├─database.php                               数据库配置文件
>│  ├─route.php                                  路由配置文件
>│  └─tags.php                                   应用行为扩展定义文件
>├─backup                                        备份目录
>├─extend                                        扩展类库目录
>│  └─rigorous                                   扩展类库标识
>├─plugins                                       插件目录
>├─public                                        公共文件目录
>│  ├─static                                     静态资源目录
>│  ├─tpl                                        模板文件目录
>│  │  ├─ModelNameA                              模型目录A
>│  │  │  ├─controller-action.jsp                模板文件
>│  │  │  └─controller-action.jsp                模板文件
>│  │  ├─ModelNameB                              模型目录B
>│  │  │  ├─controller-action.jsp                模板文件
>│  │  │  └─controller-action.jsp                模板文件
>│  │  └─ModelNameC                              模型目录C
>│  │  │  ├─controller-action.jsp                模板文件
>│  │  │  └─controller-action.jsp                模板文件
>│  ├─uplouad                                    上传文件目录
>│  └─bulid.php                                  自动生成校检文件
>├─runtime                                       运行目录
>│  ├─cache                                      项目模板缓存目录``
>│  ├─log                                        应用日志目录
>│  └─temp                                       应用缓存目录
>├─thinkphp                                      TP目录
>│  ├─lang                                       语言文件目录
>│  ├─library                                    框架类库目录
>│  │  ├─think                                   Think类库包目录
>│  │  └─traits                                  系统Trait目录
>│  ├─tpl                                        系统模板目录
>│  ├─base.php                                   基础定义文件
>│  ├─console.php                                控制台入口文件
>│  ├─convention.php                             框架惯例配置文件
>│  ├─helper.php                                 助手函数文件
>│  └─start.php                                  框架入口文件
>├─vendor                                        第三方类库目录
>│  ├─bin                                        bin目录
>│  ├─composer                                   Composer目录
>│  └─topthink                                   topthink目录
>|  |-favicon.icon                               角标文件
>|  |-rigorousMe.php                             前置配置文件
>|  |-infos.md                                   目录说明
>│  └─modelName.php                              对应模块的入口文件
>~~~
> 
>~~~
>    [git使用说明]https://jingyan.baidu.com/article/e5c39bf5c8c4d039d76033b2.html
>    
>   
>~~~
