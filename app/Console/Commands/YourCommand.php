namespace App\Console\Commands;

use Illuminate\Console\Command;

class YourCommand extends Command
{
    protected $signature = 'your:command';
    protected $description = '説明文をここに記述';

    public function handle()
    {
        // ここに実行したい処理を記述
        $this->info('コマンドが実行されました');
    }
} 