<?php 
    /*
    * Класс для генерации постраничной навигации
    */
    class Pagination
    {
        /**
        * 
        * @var Активных ссылок навигации на страницу.
        * Т.е. ссылок, помимо текущей (активной) ссылки и ссылок влево-вправо.
        * 
        */
        private $max = 6;
        
        /**
        * 
        * @var Ключ для GET, в который пишется номер страницы
        * 
        */
        private $index = 'page';
        
        /**
        * 
        * @var Текущий GET-запрос
        * 
        */
        private $query;
        
        /**
        * 
        * @var Текущая страница
        * 
        */
        private $current_page;
        
        /**
        * 
        * @var Общее количество записей
        * 
        */
        private $total; 
        
        /**
        * 
        * @var Записей на страницу
        * 
        */
        private $limit;
        
        /**
        * Запуск необходимых данных для навигации
        * @param integer $total - общее количество записей
        * @param integer $limit - количество записей на страницу
        * 
        * @return
        */
        public function __construct( $total, $limit )
        {
            # Устанавливаем общее количество записей
            $this->total  = $total;
            
            # Устанавливаем количество записей на страницу
            $this->limit  = $limit;
            
            # Устанавливаем количество страниц
            $this->amount = $this->amount();
            
            # Вызываем метод установки текущей страницы
            $this->setCurrentPage();
            
            # Вызываем метод установки текущего GET-запроса
            $this->setQueryString();
        }
        
        /**
        *  Для вывода ссылок
        * 
        * @return HTML-код со ссылками навигации
        */
        public function get()
        {
            # Для записи ссылок
            $pagination = null;
            
            # Получаем ограничения для цикла
            $limits = $this->limits();

            # Генерируем ссылки
            for($page=$limits[0]; $page<=$limits[1]; $page++)
            {
                # Формируем статус ссылки
                $status = $page == $this->current_page ? 'active' : null;
                    
                # Заносим ссылку
                $pagination .= $this->generateHtml($page, null, null, $status);
            }
            
            # Если текущая страница не первая
            if($this->current_page > 1){
                # Создаём ссылку "Предыдущая"
                $pagination = $this->generateHtml($this->current_page - 1, '<', 'Предыдущая') . $pagination;
                
                # Создаём ссылку "Первая"
                $pagination = $this->generateHtml(1, '<<', 'Первая') . $pagination;
            }
            
            # Если текущая страница не первая
            if($this->current_page < $this->amount){
                # Создаём ссылку "Следующая"
                $pagination .= $this->generateHtml($this->current_page + 1, '>', 'Следующая');  
                
                # Создаём ссылку "Следующая"
                $pagination .= $this->generateHtml($this->amount, '>>', 'Последняя');  
            }
            
            # Оборачиваем ссылки
            $pagination = '<ul class="pagination float-right">'. $pagination .'</ul>';

            # Возвращаем ссылки
            return $pagination;
        }
        
        /**
        * Для получения, откуда начинать выборку
        * 
        * @return integer
        */
        public function skip(){
            return 
                $this->current_page * $this->limit - $this->limit;
        }
        
        /**
        * Для получение ограничения выборки
        * 
        * @return integer
        */
        public function take(){
            # Получаем, откуда начинаем
            $skip = $this->skip();
            
            # Возвращаем ограницение
            return
                $skip + $this->limit > $this->total ? $this->total - $skip : $this->limit;
        }
        
        /**
        * Для генерации HTML-кода ссылки
        * @param string $query - текущий GET-запрос
        * @param integer $page - номер страницы
        * 
        * @return
        */
        private function generateHtml( $page, $text=null, $title=null, $status=null ){
            # Если текст ссылки не указан
            if( is_null($text) )
                # Указываем, что текст - цифра страницы
                $text = $page;
            
            # Формируем ссылку
            $query = $this->index .'='. $page;
            
            # Формируем строку запроса (после вопроса)
            $query = $this->query ? $this->query .'&'. $query : $query;
            
            # Формируем статус ссылки
            $status = $status ? 'class="'. $status .'"' : null;
                
            # Формируем HTML код ссылки и возвращаем
            return 
                '<li '. $status .'><a href="?'. $query .'" title="'. $title .'">'. $text .'</a></li>';
        }
        
        /**
        *  Для получения, откуда стартовать вывод ссылок
        * 
        * @return массив с началом и концом отсчёта
        */
        private function limits()
        {
            # Вычисляем ссылки слева (чтобы активная ссылка была посередине)
            $left = $this->current_page - round($this->max / 2, 0, PHP_ROUND_HALF_DOWN);

            # Вычисляем начало отсчёта
            $start = $left > 0 ? $left : 1;               

            # Если впереди есть как минимум $this->max страниц
            if($start + $this->max <= $this->amount)
                # Назначаем конец цикла вперёд на $this->max страниц или просто на минимум
                $end = $start >= 1 ? $start + $this->max : $this->max;
            else{
                # Конец - общее количество страниц
                $end = $this->amount;
 
                # Начало - минус $this->max от конца
                $start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
            }
            
            # Возвращаем
            return [$start, $end];
        }
 
        /**
        * Для установки текущей страницы
        * 
        * @return
        */
        private function setCurrentPage()
        {
            # Получаем номер страницы
            $this->current_page = isset($_GET[$this->index]) ? (int) $_GET[$this->index] : 1;
            
            # Если текущая страница боле нуля
            if($this->current_page > 0)
            {
                # Если текунщая страница меньше общего количества страниц
                if($this->current_page > $this->amount)
                    # Устанавливаем страницу на последнюю
                    $this->current_page = $this->amount;
            }
            else
                # Устанавливаем страницу на первую
                $this->current_page = 1;
        }
        
        /**
        * Для получения и установки текущего GET-запроса
        * 
        * @return
        */
        private function setQueryString(){
            # Получаем параметры текущего запроса
            $query = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
            
            # Разбираем строку запроса
            parse_str( $query, $params );
            
            # Удаляем значение страницы, если есть
            unset( $params[$this->index] );
            
            # Формируем запрос
            $this->query = http_build_query( $params );
        }
        
        /**
        * Для получеия общего числа страниц
        * 
        * @return число страниц
        */
        private function amount()
        {
            # Делим и возвращаем
            return
                ceil( $this->total / $this->limit );
        }
    }
?>
