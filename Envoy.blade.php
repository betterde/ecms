@servers(['api' => 'root@ecms.betterde.com -p 2222'])

@task('deploy')
    cd /var/local/web/sites/php/ecms/api
    git pull origin master
    @if($migrate)
        php artisan migrate
    @endif
@endtask
