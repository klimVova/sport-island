<?php
/*
Plugin Name: Cookie Notify by Loft
Description: Выводит уведомление для пользователей сайта о том, что сайт использует cookie.
License:     GPL2
 
"Cookie Notify by Loft" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version "Cookie Notify"
ABC is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "Cookie Notify by Loft". If not, see https://abc.abc/.
*/

register_activation_hook(__FILE__, 'cnl_activation');
register_deactivation_hook(__FILE__, 'cnl_deactivation');

function options()
{
    return [
        'cnl_bg' => '#000',
        'cnl_color' => '#ddd',
        'cnl_text' => 'Мы используем куки',
        'cnl_position' => 'bottom'
    ];
}

function cnl_activation()
{
    $options = cnl_options();
    foreach ($options as $key => $value) {
        update_option($key, $value);
    }
}

function cnl_deactivation()
{
    $options = cnl_options();
    foreach ($options as $key => $value) {
        delete_option($key);
    }
}

add_action('admin_menu', 'cnl_register_menu');

function cnl_register_menu()
{
    add_menu_page(
        'Cookie уведомления',
        'Cookie уведомления',
        'manage_options',
        'cnl-settings',
        'cnl_admin_page_view',
        'dashicons-buddicons-pm'
    );
}

function cnl_admin_page_view()
{
    if (!empty($_POST)) {
        update_option('cnl_bg', $_POST['cnl_bg']);
        update_option('cnl_color', $_POST['cnl_color']);
        update_option('cnl_text', $_POST['cnl_text']);
        update_option('cnl_position', $_POST['cnl_position']);
    }
    $bg = get_option('cnl_bg');
    $color = get_option('cnl_color');
    $text = get_option('cnl_text');
    $position = get_option('cnl_position')
?>
    <h2>Настройки увеломления:</h2>
    <form method="POST">
        <p>
            <label>
                Введите значение для цвета фона:
                <input type="text" name="cnl_bg" value=<?php echo $bg; ?>>
            </label>
        </p>
        <p>
            <label>
                Введите значение для цвета текста:
                <input type="text" name="cnl_color" value=<?php echo $color; ?>>
            </label>
        </p>
        <p>
            <label>
                Введите текст уведомления:
                <input type="text" name="cnl_text" value="<?php echo $text; ?>">
            </label>
        </p>
        <fieldset>
            <legend>
                Выберите положение для уведомления:
            </legend>
            <label>
                Сверху
                <input type="radio" name="cnl_position" value="top" <?php checked('top', $position, true); ?>>
            </label>
            <label>
                Снизу
                <input type="radio" name="cnl_position" value="bottom" <?php checked('bottom', $position, true); ?>>
            </label>
        </fieldset>
        <br>
        <button type="submit">Сохранить настройки</button>
    </form>
    <?php
}


add_action('wp_footer', 'cnl_front_page_view');

function cnl_front_page_view()
{
    if ($_COOKIE['cnl_cookie_agreement'] !== 'agreed') :
        $bg = get_option('cnl_bg');
        $color = get_option('cnl_color');
        $text = get_option('cnl_text');
        $position = get_option('cnl_position');
        $css = $position . ': 0;';
    ?>
        <div class="alert">
            <div class="wrapper">
                <?php echo $text; ?>
                <br>
                <button class="alert__btn">Я согласен</button>
            </div>
            <style>
                .alert {
                    color: <?php echo $color; ?>;
                    background-color: <?php echo $bg; ?>;
                    position: fixed;
                    <?php echo $css; ?>left: 0;
                    z-index: 9999999;
                    text-align: center;
                    font-size: 30px;
                    padding: 20px 10px;
                    width: 100%;
                }

                .alert button {
                    border: 1px solid <?php echo $color; ?>;
                    background-color: transparent;
                    font: inherit;
                    font-size: 14px;
                    color: <?php echo $color; ?>;
                    padding: 10px 20px;
                    cursor: pointer;
                }

                .alert button:hover,
                .alert button:active,
                .alert button:focus {
                    background-color: <?php echo $color; ?>;
                    color: <?php echo $bg; ?>;
                    transition: 0.3s;
                }
            </style>
            <script>
                const url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                const btn = document.querySelector('.alert__btn');
                btn.addEventListener('click', function(e) {
                    const data = new FormData();
                    data.append('action', 'cnl_cookie_ajax');
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', url);
                    xhr.send(data);
                    xhr.addEventListener('readystatechange', function() {
                        if (xhr.readyState !== 4) return;
                        if (xhr.status === 200) {
                            btn.parentElement.parentElement.remove();
                        }
                    });
                });
            </script>
        </div>
<?php
    endif;
}

add_action('wp_ajax_nopriv_cnl_cookie_ajax', 'cnl_ajax_handler');
add_action('wp_ajax_cnl_cookie_ajax', 'cnl_ajax_handler');

function cnl_ajax_handler()
{
    setcookie('cnl_cookie_agreement', 'agreed', time() + 60 * 60 * 24 * 30, '/');
    echo  'OK';
    wp_die();
}
