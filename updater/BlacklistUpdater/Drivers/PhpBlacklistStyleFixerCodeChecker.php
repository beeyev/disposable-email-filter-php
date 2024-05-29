<?php
/*
 * (c) Alexander Tebiev - https://github.com/beeyev
 */
declare(strict_types=1);

namespace Beeyev\DisposableEmailFilter\Updater\BlacklistUpdater\Drivers;

use PhpCsFixer\Config as CsConfig;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ToolInfo;
use Symfony\Component\EventDispatcher\EventDispatcher;

/** @internal */
final class PhpBlacklistStyleFixerCodeChecker implements PhpBlacklistStyleFixerCodeCheckerInterface
{
    /** @var non-empty-string */
    private $filePath;

    /**
     * @param non-empty-string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function fixAndCheck(): void
    {
        $this->fix();
        $this->check();
    }

    private function fix(): void
    {
        $config = new CsConfig();
        $config->setRules([
            '@PhpCsFixer' => true,
            '@PSR12' => true,
        ]);

        $cwd = getcwd();
        assert($cwd !== false);

        $resolver = new ConfigurationResolver(
            $config,
            ['dry-run' => false, 'stop-on-violation' => true],
            $cwd,
            new ToolInfo()
        );

        $finder = Finder::create()->append([$this->filePath]);
        $errorsManager = new ErrorsManager();

        (new Runner(
            $finder,
            $resolver->getFixers(),
            $resolver->getDiffer(),
            new EventDispatcher(),
            $errorsManager,
            $resolver->getLinter(),
            $resolver->isDryRun(),
            $resolver->getCacheManager(),
            $resolver->getDirectory(),
            $resolver->shouldStopOnViolation()
        ))->fix();

        if (count($errorsManager->getInvalidErrors()) > 0) {
            $error = $errorsManager->getInvalidErrors()[0]->getSource();
            assert($error instanceof \Throwable);

            throw new \RuntimeException($error->getMessage(), $error->getCode(), $error);
        }
    }

    private function check(): void
    {
        $result = require $this->filePath;

        assert(is_array($result));
        assert(isset($result['updated_at']));
        assert(isset($result['disposable_email_domains']));

        assert($result['updated_at'] instanceof \DateTimeImmutable);

        if (count($result['disposable_email_domains']) === 0) {
            throw new \RuntimeException('Given file does not contain any data.');
        }
    }
}
