
cd workPath/

git config --global user.name "rigorouseMe"

git config --global user.email "dream_donghao@163.com"

git init

git remote add origin https://github.com/rigorouseMe/rigorouseMe.git 初始化配置

git clone https://github.com/rigorouseMe/rigorouseMe.git #克隆下载

git pull origin master

git add .

git commit -m "init"

git push origin master

其他常见git命令

查看所有分支  ：git branch -a

切换到某一分支：git checkout  分支名称

合并分支：git merge 原分支  目标分支

http://git.mydoc.io/?t=180676
