<?php

declare(strict_types=1);

namespace App\Copilot;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class CopilotRuntime
{
    /**
     * Copilot CLIを実行して結果を取得する。
     * ローカルで動かす場合は通常のcopilotの認証が使われる。
     * GitHub Actionsで動かす場合はGH_TOKEN,COPILOT_GITHUB_TOKENなどの環境変数の設定が必要。
     */
    public static function run(string $prompt): string
    {
        $result = Process::input($prompt)->run('copilot --allow-all-tools');

        if ($result->successful()) {
            info($result->output());

            // そのままの実行結果には余計な情報が含まれているため、ファイルから取得する
            // プロンプトの段階で結果はファイルに書き出すように指示する。
            if (Storage::exists('copilot.md')) {
                $response = Storage::get('copilot.md');
                // Storage::delete('copilot.md');

                return $response ?? '';
            }
        }

        logger()->error('Command failed: '.$result->errorOutput());

        return '';
    }
}
