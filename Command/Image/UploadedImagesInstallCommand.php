<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Car\Command\Image;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'baks:reference-car:upload:install',
    description: 'Копирует загруженные изображения в публичную директорию',
)]
final class UploadedImagesInstallCommand extends Command
{
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();

        $this->projectDir = $kernel->getProjectDir();
    }

    private string $projectDir;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->newLine();
        $io->text('Копирование изображений');
        $io->newLine();


        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()?->getKernel();

        $publicDir = $this->projectDir.DIRECTORY_SEPARATOR.$this->getPublicDirectory($kernel->getContainer());


        if(false === is_dir($publicDir))
        {
            throw new InvalidArgumentException(sprintf('Каталог "%s" не существует.', $publicDir));
        }


        $imagePath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            'Resources',
            'upload',
        ]);

        $Filesystem = new Filesystem();

        if($Filesystem->exists($imagePath))
        {
            $finder = new Finder();
            $finder->files()->in($imagePath);


            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $targetFile = $publicDir.'/upload/'.$file->getRelativePath().'/'.$file->getFilename();
                $Filesystem->copy($file->getRealPath(), $targetFile);
            }
        }

        $io->text('Все изображения были успешно скопированы');
        $io->newLine();
        return Command::SUCCESS;
    }

    private function getPublicDirectory(ContainerInterface $container): string
    {
        $defaultPublicDir = 'public';

        if(null === $this->projectDir && !$container->hasParameter('kernel.project_dir'))
        {
            return $defaultPublicDir;
        }

        $composerFilePath = ($this->projectDir ?? $container->getParameter('kernel.project_dir')).'/composer.json';

        if(false === file_exists($composerFilePath))
        {
            return $defaultPublicDir;
        }

        $composerConfig = json_decode(
            file_get_contents($composerFilePath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $composerConfig['extra']['public-dir'] ?? $defaultPublicDir;
    }
}