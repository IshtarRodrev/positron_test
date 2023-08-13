
### **Установка**

- `docker-compose up -d --build`
- `docker-compose exec php composer install`
- `docker-compose exec php bin/console doctrine:migrations:migrate`

### **Загрузка книг**
Необходимо авторизироваться в админке(`/admin`). лог:пас `admin`:`admin`

В разделе "Site settings" указать URL с которого парсить. Например `https://gitlab.grokhotov.ru/hr/yii-test-vacancy/-/raw/master/books.json#L3260`
Запустить команду:
- `docker-compose exec php bin/console app:parse`
