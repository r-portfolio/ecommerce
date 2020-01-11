<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class User extends Model
{
    public const SESSION = 'User';

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

        if (true === password_verify($password, $data['despassword'])) {
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
}