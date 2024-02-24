# GetRatePHP
 
Технічне завдання проєкту

Мета 
    Розробити сайт, який зможе: 
    - прокладати маршрути вантажних перевезень, 
    - обчислювати їх вартість 
    = та орїєнтовний час перевезення. 

Background

    Я працюю в логистичной компанії. Трошки даних:
    - 300-400 замовників послуг
    - 1500 постачальників послуг
    - 30 сейлзів

    Ми живемо в умовах війни. Звичайна проста логістика (через великі порти Одеси) неможлива. Тобто нам потрібно розробляти складні маршрути з різними відами транспорту.
    Та враховувати різні обставини (блокування кордонів, хусіти в Червоному морі та інші).
    Вартість перевезеня постачальники можуть змінювати по декілька разів на місяць.

    Наш клієнт - це зазвичай трейдер - який хоче привезти товари в України, чи купити в Україні. Від нас йому потрібен швідкий прорахунок перевезень. Зараз, він звертається до нашого сейлза, та чекає від 10 хвилин до 1 доби в очикуванні котирування.

Що планую зробити:

    1.  Отримати із діючої програми компанії:
            - всі можливі пункти маршрутов перевезень,
            = існуючі віди транспорту, які їх можуть з'єднувати,
            - транзитний час перевезення по кожному відрізку,
        та внести їх в бд сайта.

    2.  При отримані запита на сайті (з початковим пунктом та кінцевим), обчисляти         всі можливі складні маршрути перевезення.

    3. Надсилати запит(и) до діючої програми, щодо отримання діючої вартості перевезення
    від постачальників по кожному відрізку.

    4. Вивести результати побудови маршрутів на сайті. Відобразити відрізки, тип транспорту, постачальника та транзитний час. Зробити можливим сортування отриманих результатів по вартості та часу перевезення.


