服务加载项
    composer国内源
        阿里云镜像
            https://mirrors.aliyun.com/composer/
        腾讯云镜像
            https://mirrors.cloud.tencent.com/composer/
        华为云镜像
            https://repo.huaweicloud.com/repository/php/
     配置composer源地址
        全局配置
            composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
            取消全局配置
                composer config -g --unset repos.packagist
        项目配置
            composer config repo.packagist composer https://mirrors.aliyun.com/composer/
            取消配置
                composer config --unset repos.packagist
    运行命令
        composer install
    安装完成即可启动服务
