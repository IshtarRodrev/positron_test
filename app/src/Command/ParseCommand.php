<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Settings;
use App\Enum\BookStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

#[AsCommand(
    name: 'app:Parse',
    description: 'Парсит список книг из файла https://gitlab.grokhotov.ru/hr/yii-test-vacancy/-/blob/master/books.json',
    aliases: ['app:add-user'],
    hidden: false,
)]
class ParseCommand extends Command
{
    const CATEGORY_NEW_ID = 1; // id категории "Новинки", которая была добавлена миграцией

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        // 3. Update the value of the private entityManager variable through injection
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')

            ->setHelp('Помоги себе сам...')
        ;
    }

    private function curlGetContents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->entityManager;
        $io = new SymfonyStyle($input, $output);

        $repoSettings = $em->getRepository(Settings::class);
        $repoBook = $em->getRepository(Book::class);
        $repoAuthor = $em->getRepository(Author::class);
        $repoCategory = $em->getRepository(Category::class);

        $noCategory = $repoCategory->find( self::CATEGORY_NEW_ID);
        if (!$noCategory) {
            $io->error('Error! No category "New"');
            return Command::FAILURE;
        }

        $settings = $repoSettings->getSettings();

        $output->writeln('PARSE_URL: ' . $settings->getParseUrl());

        $parseUrl = $settings->getParseUrl();

        $booksStr = file_get_contents($parseUrl);
        $booksJson = json_decode($booksStr, true);

        $output->writeln('*** Прочитан файл. Найдено книг ' . count($booksJson) . ' ***');

        $i = 0;
        foreach ($booksJson as $bookJson) {

            if (empty($bookJson['isbn'])) {
                $io->warning('Warning! Skip book "' . ($bookJson['title'] ?? 'UNKNOWN') . '". No ISBN.');
                continue;
            }

            $book = $repoBook->findOneBy(['isbn' => $bookJson['isbn']]);
            if ($book) {
                $output->writeln('Книга  (' . $bookJson['isbn'] . ')  уже есть в базе: "' . $bookJson['title'] . '"');
                continue;
            }
            $i++;
            $book = new Book();
            $book->setTitle($bookJson['title']);
            $book->setIsbn($bookJson['isbn']);
            $book->setPages($bookJson['pageCount'] ?? 0);

            $stringDate = $bookJson['publishedDate']['$date'] ?? '';
            $publishedDate = new \DateTimeImmutable;
            $publishedDate::createFromFormat(DATE_ATOM, $stringDate); //: DateTimeImmutable|false

            $book->setPublished($publishedDate);

            if (!empty($bookJson['thumbnailUrl'])) {
                $output->writeln('image url: ' . $bookJson['thumbnailUrl']);

                $pathUploader = realpath(__DIR__ . '/../../public/uploads/images/') . '/';

                $pathParts = pathinfo($bookJson['thumbnailUrl']);
                $imageName = sprintf('%s.%s', md5(random_int(1, 999) . $pathParts['filename'] . $pathParts['extension']), $pathParts['extension']);

                $pathToImage = $pathUploader . $imageName;

                $sourceFile = $this->curlGetContents($bookJson['thumbnailUrl']);
//                $sourceFile = fopen($bookJson['thumbnailUrl'], 'r');
                if (!$sourceFile || !file_put_contents($pathToImage, $sourceFile)) {
                    $io->warning('Warning! Skip book "' . ($bookJson['title'] ?? 'UNKNOWN') . '". Failed downloaw image.');
                    continue;
                }

                $book->setCoverImage($imageName);
            } else {
                $book->setCoverImage('');
            }

            $book->setShortDescription($bookJson['shortDescription'] ?? '');
            $book->setLongDescription($bookJson['longDescription'] ?? '');

            $status = $bookJson['status'] ?? 'UNKNOWN';
            $book->setStatus(BookStatus::FromString($status)->value);

            $repoBook->add($book);

            if (!empty($bookJson['authors']) && is_array($bookJson['authors'])) {
                foreach ($bookJson['authors'] as $authorName) {
                    $categoryName = trim($authorName);
                    if (empty($authorName))
                        continue;

                    $author = $repoAuthor->findOneBy(['name' => $authorName]);
                    if (!$author) {
                        $author = new Author();
                        $author->setName($authorName);
                        $repoAuthor->add($author);
                    }

                    $repoBook->addAuthor($book, $author);
                }
            }

            $addedCategory = false;
            if (!empty($bookJson['categories']) && is_array($bookJson['categories'])) {
                foreach ($bookJson['categories'] as $categoryName) {
                    $categoryName = trim($categoryName);
                    if (empty($categoryName))
                        continue;

                    $category = $repoCategory->findOneBy(['category_name' => $categoryName]);
                    if (!$category) {
                        $category = new Category();
                        $category->setCategoryName($categoryName);
                        $repoCategory->add($category);
                    }

                    $repoBook->addCategory($book, $category);
                    $addedCategory = true;
                }
            }

            if (!$addedCategory) {
                $repoBook->addCategory($book, $noCategory);
            }

            $output->writeln('Добавлена новая книга  "' . $bookJson['title'] . '"(' . $bookJson['isbn'] . ')');
        }

        $io->success(count($booksJson) . ' books have been uploaded successfully! ' . $i . ' books was added.');

        return Command::SUCCESS;
    }
}
