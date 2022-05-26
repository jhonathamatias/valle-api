<?php namespace Valle\Sessions;

use Valle\Factorys\RepositoryFactory;
use Valle\Libs\Repository;

use SessionHandler;

class EduOSSessionHandler extends SessionHandler
{
    /**
     * @var Repository $repository
     */
    protected $repository;

    /**
     * @var bool $exists
     */
    protected $exists;

    public function __construct(RepositoryFactory $repository)
    {
        $this->repository = $repository->get('sessions');
    }
    /**
     * Destroy a session
     * @link https://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($session_id)
    {
        $this->removeCookie();

        $this->repository->executeQuery('DELETE FROM sessions WHERE sess_id = ?', [$session_id]);

        return true;
    }
    /**
     * Cleanup old sessions
     * @link https://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $maxlifetime <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function gc($maxlifetime)
    {
        $expired_time = time() - $maxlifetime;
        $this->repository->executeQuery('DELETE FROM sessions WHERE last_activity <= ?', [$expired_time]);
        return true;
    }
    /**
     * Read session data
     * @link https://php.net/manual/en/sessionhandlerinterface.read.php
     * @param string $session_id The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function read($session_id)
    {
        $session = $this->repository->where('sess_id = ?', [$session_id])[0] ?? null;

        $this->exists = (false === is_null($session));
        return $this->exists ? $session['sess_data'] : '';
    }
    /**
     * Write session data
     * @link https://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function write($session_id, $session_data)
    {
        if ($this->exists === false) {
            return $this->add($session_id, $session_data);
        }
        return $this->upgrade($session_id, $session_data);
    }
    /**
     * ADD new session in database
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (always TRUE on success).
     * </p>
     */
    private function add($session_id, $session_data)
    {
        $this->repository->insert([
            'sess_id' => $session_id,
            'sess_data' => $session_data,
            'last_activity' => time()
        ]);
        $this->exists = true;

        return true;
    }
    /**
     * update a datas of session.
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (always TRUE on success).
     * </p>
     */
    private function upgrade($session_id, $session_data)
    {
        $this->repository->updateEquals([
            'sess_data' => $session_data,
            'last_activity' => time()
        ], ['sess_id' => $session_id]);

        return true;
    }
    /**
     * Remove data of cookie
     * <p> This method remove PHPSESSID in user browser,
     * the measure for protection of system.
     * </p>
     * @return void
     */
    private function removeCookie()
    {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
}