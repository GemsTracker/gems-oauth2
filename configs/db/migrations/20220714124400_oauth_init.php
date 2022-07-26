<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OauthInit extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $exists = $this->hasTable('gems__oauth_access_tokens');
        if (!$exists) {
            $accessTokens = $this->table('gems__oauth_access_tokens', ['signed' => false]);
            $accessTokens
                ->addColumn('access_token', 'string', ['limit' => 100])
                ->addColumn('user_id', 'integer', ['signed' => false])
                ->addColumn('client_id', 'string', ['limit' => 255])
                ->addColumn('scopes', 'text', ['null' => true])
                ->addColumn('revoked', 'boolean')
                ->addColumn('expires_at', 'datetime')
                ->addTimestamps()
                ->addIndex(['access_token', 'user_id'])
                ->create();
        }

        $exists = $this->hasTable('gems__oauth_auth_codes');
        if (!$exists) {
            $authCodes = $this->table('gems__oauth_auth_codes', ['signed' => false]);
            $authCodes
                ->addColumn('auth_code', 'string', ['limit' => 100])
                ->addColumn('user_id', 'string', ['limit' => 255])
                ->addColumn('client_id', 'string', ['limit' => 255])
                ->addColumn('scopes', 'text', ['null' => true])
                ->addColumn('redirect', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('revoked', 'boolean')
                ->addColumn('expires_at', 'datetime')
                ->addTimestamps()
                ->addIndex(['auth_code', 'user_id'])
                ->create();
        }

        $exists = $this->hasTable('gems__oauth_clients');
        if (!$exists) {
            $clients = $this->table('gems__oauth_clients', ['signed' => false]);
            $clients
                ->addColumn('client_id', 'string', ['limit' => 255])
                ->addColumn('name', 'string', ['limit' => 255])
                ->addColumn('secret', 'string', ['limit' => 255])
                ->addColumn('redirect', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('active', 'boolean')
                ->addColumn('confidential', 'boolean')
                ->addTimestamps()
                ->addIndex(['client_id'])
                ->create();
        }

        $exists = $this->hasTable('gems__oauth_refresh_tokens');
        if (!$exists) {
            $refreshTokens = $this->table('gems__oauth_refresh_tokens', ['signed' => false]);
            $refreshTokens
                ->addColumn('refresh_token', 'string', ['limit' => 100])
                ->addColumn('access_token', 'string', ['limit' => 100])
                ->addColumn('revoked', 'boolean')
                ->addColumn('expires_at', 'datetime', ['null' => true])
                ->addTimestamps()
                ->addIndex(['refresh_token'])
                ->create();
        }

        $exists = $this->hasTable('gems__oauth_scopes');
        if (!$exists) {
            $clients = $this->table('gems__oauth_scopes', ['signed' => false]);
            $clients
                ->addColumn('name', 'string', ['limit' => 255])
                ->addColumn('description', 'string', ['limit' => 255])
                ->addColumn('active', 'boolean')
                ->addTimestamps()
                ->addIndex(['name'])
                ->create();
        }

        $exists = $this->hasTable('gems__oauth_mfa_codes');
        if (!$exists) {
            $authCodes = $this->table('gems__oauth_mfa_codes', ['signed' => false]);
            $authCodes
                ->addColumn('mfa_code', 'string', ['limit' => 100])
                ->addColumn('auth_method', 'string', ['limit' => 32])
                ->addColumn('user_id', 'string', ['limit' => 255])
                ->addColumn('client_id', 'string', ['limit' => 255])
                ->addColumn('scopes', 'text', ['null' => true])
                ->addColumn('revoked', 'boolean')
                ->addColumn('expires_at', 'datetime')
                ->addTimestamps()
                ->addIndex(['id', 'user_id'])
                ->create();

        }
    }
}
