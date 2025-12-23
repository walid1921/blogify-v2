<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Blog;
use App\Entity\BlogCategories;
use App\Entity\Likes;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{

    public function testCreateBlog (): void
    {
        // Arrange
        $blog = new Blog();
        $createdAt = new DateTimeImmutable();

        // Act
        $blog->setTitle('title');
        $blog->setContent('content');
        $blog->setCreatedAt($createdAt);
        $blog->setIsPublished(true);
        $blog->setReadTime(2);
        $blog->setBlogLanguage("English");
        $blog->setCoverImage("imgURL");

        // Assert
        self::assertSame('title', $blog->getTitle());
        self::assertSame('content', $blog->getContent());
        self::assertSame($createdAt, $blog->getCreatedAt());
        self::assertTrue($blog->isPublished());
        self::assertSame(2, $blog->getReadTime());
        self::assertSame('English', $blog->getBlogLanguage());
        self::assertSame('imgURL', $blog->getCoverImage());
    }



    // ✔ Adding one category → Blog should have 1 item in categories
    // ✔ Removing category → List should be empty
    // ✔ Ensure BlogCategories::addBlog() creates the relationship on both sides
    public function testAddAndRemoveCategory (): void
    {

        // Arrange
        $blog = new Blog();
        $category = new BlogCategories();
        $category->setName('A category name');

        // Act — add category to blog
        $blog->addCategory($category);

        // Assert — Blog side
        self::assertCount(1, $blog->getCategories());
        self::assertTrue($blog->getCategories()->contains($category));

        // Assert — Category side (bidirectional)
        self::assertCount(1, $category->getBlogs());
        self::assertTrue($category->getBlogs()->contains($blog));

        // Act — remove category
        $blog->removeCategory($category);

        // Assert — Blog side
        self::assertCount(0, $blog->getCategories());

        // Assert — Category side
        self::assertCount(0, $category->getBlogs());

    }


    // ✔ Blog::addLike() adds a Like
    // ✔ When adding a Like → the Like should point to the Blog ($like->getBlog() === $blog)
    // ✔ Blog::removeLike() removes AND resets Like→blog to null
    // Tests both sides of the relationship
    // Blog → Likes
    // Like → Blog
    public function testAddAndRemoveLike (): void
    {
        // Arrange
        $blog = new Blog();
        $like = new Likes();

        // Act — add like
        $blog->addLike($like);

        // Assert — Blog side
        self::assertCount(1, $blog->getLikes());
        self::assertTrue($blog->getLikes()->contains($like));

        // Assert — Like side (owning side)
        self::assertSame($blog, $like->getBlog());

        // Act — remove like
        $blog->removeLike($like);

        // Assert — Blog side
        self::assertCount(0, $blog->getLikes());

        // Assert — Like side reset
        self::assertNull($like->getBlog());
    }



    // ✔ If excerpt is manually set, getExcerpt() returns it.
    // ✔ If content contains JSON with blocks, the first paragraph text becomes the excerpt.
    // ✔ If JSON invalid → return empty string "".

    // Manually set excerpt is returned
    public function testGetExcerptReturnsManualExcerpt (): void
    {
        // Arrange
        $blog = new Blog();
        $blog->setExcerpt('Manual excerpt text');

        // Act
        $excerpt = $blog->getExcerpt();

        // Assert
        self::assertSame('Manual excerpt text', $excerpt);
    }

    // Extract excerpt from JSON content
    public function testGetExcerptExtractsFromJsonContent (): void
    {
        // Arrange
        $blog = new Blog();

        $content = json_encode([
            'blocks' => [
                [
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'This is the first paragraph excerpt'
                    ]
                ],
                [
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Another paragraph'
                    ]
                ]
            ]
        ]);

        $blog->setContent($content);

        // Act
        $excerpt = $blog->getExcerpt();

        // Assert
        self::assertSame('This is the first paragraph excerpt', $excerpt);
    }


    // Invalid JSON returns empty string
    public function testGetExcerptReturnsEmptyStringOnInvalidJson (): void
    {
        // Arrange
        $blog = new Blog();
        $blog->setContent('this is not valid json');

        // Act
        $excerpt = $blog->getExcerpt();

        // Assert
        self::assertSame('', $excerpt);
    }


    public function setUp (): void
    {
        parent::setUp();
        // $blog = new Blog();
    }
}
