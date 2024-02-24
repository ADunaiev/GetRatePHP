<div id="sign-up-result">
        This is message page
</div>
<p>
        <?php 
                
                if(isset( $_SESSION['user-id'])) {
                        echo "Дані є. А ти йолопе не може до них дістатися.";
                }  
                else {
                        echo "Нічого немає.";
                }       
                echo "</br>";
                if(isset( $_SESSION['user-email'])) {
                        echo "Дані є. А ти йолопе не може до них дістатися.";
                        }  
                        else {
                        echo "Нічого немає.";
                        }  
        ?>
</p>
