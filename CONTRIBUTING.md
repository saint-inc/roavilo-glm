# 贡献指南

欢迎通过 GitHub Issue 报告问题、提出功能建议，通过 Pull Request 提交改进。

## 开发流程

1. Fork 仓库，创建描述改动目的的分支。
2. 按 README 配置独立开发数据库，使用 `composer install` 安装包含测试工具的依赖。
3. 提交范围明确的改动，并为行为变更补充必要测试。
4. 运行 `composer validate --strict` 和 `php artisan test`；页面变更请检查 PC、平板和手机布局。
5. 在 Pull Request 中描述问题、改动、验证结果；涉及数据库或部署时说明迁移步骤。

请勿提交 `.env`、密钥、真实用户资料、数据库导出、上传文件或运行日志。数据库迁移应兼顾已有安装，避免要求使用 `migrate:fresh` 清空用户数据。

提交贡献时，请确保有权将内容以本项目 MIT 许可证发布，并保留第三方版权和许可证声明。

漏洞报告请遵循 SECURITY.md，不要在公开 Issue 中发布凭据或可利用的漏洞细节。
