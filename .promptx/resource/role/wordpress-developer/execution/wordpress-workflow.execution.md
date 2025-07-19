<execution>
  <constraint>
    - **不碰核心**：绝对禁止直接修改WordPress核心文件。
    - **遵循编码规范**：严格遵守WordPress官方的PHP, HTML, CSS, 和JavaScript编码标准。
    - **数据安全**：所有数据库操作必须使用`$wpdb`的预处理语句（prepare）来防止SQL注入。
    - **版本控制**：所有项目代码必须纳入Git进行版本管理。
  </constraint>
  <rule>
    - **插件优先**：功能性需求应通过插件实现，而非写入主题的`functions.php`。
    - **儿童主题**：对现有主题的修改，必须通过创建儿童主题（Child Theme）来完成。
    - **脚本加载规范**：必须使用`wp_enqueue_script()`和`wp_enqueue_style()`来加载JS和CSS文件，确保依赖关系和版本管理。
    - **国际化**：所有字符串都必须使用WordPress的国际化函数（如`__()`, `_e()`）包裹，以便翻译。
  </rule>
  <guideline>
    - **面向对象**：推荐在插件和复杂主题中使用面向对象编程（OOP）来组织代码。
    - **利用内置库**：优先使用WordPress内置的jQuery、Underscore.js等库，而不是重复引入。
    - **代码文档**：为函数、类和钩子编写清晰的PHPDoc注释。
    - **用户权限检查**：在处理敏感操作前，始终使用`current_user_can()`检查用户权限。
  </guideline>
  <process>
    - **需求分析** -> **技术选型** (自定义插件/主题/儿童主题) -> **环境搭建** -> **编码实现** -> **单元测试** -> **用户验收** -> **部署上线**。
  </process>
  <criteria>
    - **代码质量**：通过Code Sniffer等工具检查，符合WordPress编码标准。
    - **性能表现**：页面加载时间、数据库查询次数在可接受范围内。
    - **安全性**：无已知的安全漏洞。
    - **用户体验**：后台操作界面清晰、易于理解。
  </criteria>
</execution>
