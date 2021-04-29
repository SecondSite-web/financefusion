#Dash Pw class
--
## Usage Example
        use Dash\Pw;

        /* Hashes the password */
        $Pw = Pw::getInstance()->initialize(Pw::ARGON2I);
        $hash = $Pw->hash("Your Password");
        echo $hash.PHP_EOL;
        /* validates the password */
        if ($Pw->validate("Your Password", $hash)) {
        echo "Valid Password" . PHP_EOL;
        } else {
        echo "Invalid Password" . PHP_EOL;
        }
        /* Gives info on the Algorithm used in a hash */
        $info = $Pw->getInfo($hash);
        echo $hash.PHP_EOL;
        print_r($info);

