<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Mailer;
use Hcode\Model;

class User extends Model
{
    public const SESSION = 'User';
    // Key base_64
    public const SECRET = 'HcodePhp7_Secret';

    /**
     * login.
     *
     * @param string $name
     * @param mixed  $login
     * @param mixed  $password
     *
     * @return array $user com valores do usuario
     */
    public static function login($login, $password)
    {
        $sql = new Sql();

        $results = $sql->select('SELECT * FROM tb_users WHERE deslogin = :LOGIN', [
          ':LOGIN' => $login,
        ]);

        if (0 === \count($results)) {
            throw new \Exception('Usuário inexistente ou senha inválida.');
        }

        $data = $results[0];

        if (true == password_verify($password, $data['despassword'])) {
            $user = new self();
            $user->setData($data);
            $_SESSION[self::SESSION] = $user->getValues();

            return $user;
        }
        throw new \Exception('Usuário inexistente ou senha inválida.');
    }

    /**
     * verifyLogin.
     *
     * @param mixed $inadmin
     */
    // Verifica se o usuário logado tem perfil admin
    public static function verifyLogin($inadmin = true)
    {
        if (!isset($_SESSION[self::SESSION])
            ||
            !$_SESSION[self::SESSION]
            ||
            !(int) $_SESSION[self::SESSION]['iduser'] > 0
            ||
            (bool) $_SESSION[self::SESSION]['inadmin'] !== $inadmin
            ) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function logout()
    {
        $_SESSION[self::SESSION] = null;
    }

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select('SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson');
    }

    // Criado procedure para evitar grande volume de requisições no banco
    // Realiza insert no banco
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select('CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)', [
            ':desperson' => $this->getdesperson(),
            ':deslogin' => $this->getdeslogin(),
            ':despassword' => $this->getdespassword(),
            ':desemail' => $this->getdesemail(),
            ':nrphone' => $this->getnrphone(),
            ':inadmin' => $this->getinadmin(),
        ]);
        $this->setData($results[0]);
    }

    /**
     * get.
     *
     * @param mixed $iduser
     * @param int   $iduser carrega o id do usuário
     */
    // Carrega dados do usuário para edição
    public function get($iduser)
    {
        $sql = new Sql();

        $results = $sql->select('SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser', [
            ':iduser' => $iduser,
        ]);

        $this->setData($results[0]);
    }

    // Criado procedure para evitar requisições no banco

    /**
     * update.
     */
    public function update()
    {
        $sql = new Sql();

        $results = $sql->select('CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)', [
            ':iduser' => $this->getiduser(),
            ':desperson' => $this->getdesperson,
            ':deslogin' => $this->getdeslogin(),
            ':despassword' => $this->getdespassword,
            ':desemail' => $this->getdesemail(),
            ':nrphone' => $this->getnrphone(),
            ':inadmin' => $this->getinadmin(),
        ]);

        $this->setData($results);
    }

    // Criado procedure para evitar requisições no banco
    public function delete()
    {
        $sql = new Sql();
        $sql->query('CALL sp_users_delete(:iduser)', [
            'iduser' => $this->getiduser(),
        ]);
    }

    /**
     * getForgot.
     *
     * @param string $email   recebe um email cadastrado
     * @param mixed  $email
     * @param mixed  $inadmin
     */
    // Redefine a senha do usuário
    public static function getForgot($email, $inadmin = true)
    {
        $sql = new Sql();
        $results = $sql->select('
         SELECT *
         FROM tb_persons a
         INNER JOIN tb_users b USING(idperson)
         WHERE a.desemail = :email;
     ', [
         ':email' => $email,
     ]);
        if (0 === \count($results)) {
            throw new \Exception('Não foi possível recuperar a senha.');
        }

        $data = $results[0];
        $resultsRecovery = $sql->select('CALL sp_userspasswordsrecoveries_create(:iduser, :desip)', [
             ':iduser' => $data['iduser'],
             ':desip' => $_SERVER['REMOTE_ADDR'],
         ]);
        if (0 === \count($resultsRecovery)) {
            throw new \Exception('Não foi possível recuperar a senha.');
        }

        $dataRecovery = $resultsRecovery[0];
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', self::SECRET, 0, $iv);
        $result = base64_encode($iv.$code);
        if (true === $inadmin) {
            $link = "http://www.ecommerce.com.br/admin/forgot/reset?code=$result";
        } else {
            $link = "http://www.ecommerce.com.br/forgot/reset?code=$result";
        }
        $mailer = new Mailer($data['desemail'], $data['desperson'], 'Redefinir senha da Hcode Store', 'forgot', [
                 'name' => $data['desperson'],
                 'link' => $link,
             ]);
        $mailer->send();

        return $link;
    }

    /**
     * validForgotDecrypt.
     *
     * @param mixed $result
     */
    public static function validForgotDecrypt($result)
    {
        $result = base64_decode($result);
        $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
        $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
        $idrecovery = openssl_decrypt($code, 'aes-256-cbc', self::SECRET, 0, $iv);
        $sql = new Sql();
        $results = $sql->select('
         SELECT *
         FROM tb_userspasswordsrecoveries a
         INNER JOIN tb_users b USING(iduser)
         INNER JOIN tb_persons c USING(idperson)
         WHERE
         a.idrecovery = :idrecovery
         AND
         a.dtrecovery IS NULL
         AND
         DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
     ', [
         ':idrecovery' => $idrecovery,
     ]);
        if (0 === \count($results)) {
            throw new \Exception('Não foi possível recuperar a senha.');
        }

        return $results[0];
    }

    /**
     * setForgotUsed.
     *
     * @param mixed $idrecovery
     */
    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();

        $sql->query('UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery ', [
              ':idrecovery' => $idrecovery,
        ]);
    }

    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query('UPDATE tb_users SET despassword = :password WHERE iduser = :iduser', [
           ':password' => $password,
           ':iduser' => $this->getiduser(),
        ]);
    }
}