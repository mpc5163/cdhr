# php-excel
    方便快捷的 Excel 导出导入类，就俩静态方法

## 简单上手

### 导出
```php
use YExcel\Excel;

$data = [...]; // 从数据库之类得到数据，数组或生成器，使用生成器方式可以节省内存使用
$template = 'template.xlsx'; // 模板文件，如果不提供则新建一个 xlsx 文件

// 给浏览器发送下载头
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=sites_export.xlsx');
header('Cache-Control: max-age=0');

// 默认跳过第一行表头
// 更多使用请查看 src\Excel 注释
Excel::put('php://output', $data, $template);
```

### 导入
```php
use YExcel\Excel;

// 默认跳过第一行表头
// 更多使用请查看 src\Excel 注释
foreach (Excel::get('file.xlsx') as $row) {
    var_dump($row);
    // 写入数据库之类
}
```

## 测试
```bash
cd test
php test.php
```

