<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit;

use Cog\Likeable\Contracts\Like as LikeContract;
use Cog\Likeable\Tests\Stubs\Models\Entity;
use Cog\Likeable\Tests\Stubs\Models\User;
use Cog\Likeable\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class HasLikesTest.
 *
 * @package Cog\Likeable\Tests\Unit\Traits
 */
class HasLikesTest extends TestCase
{
    use DatabaseTransactions;

    /* Likes */

    /** @test */
    public function it_can_like_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->like();

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_like_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $entity->like($user2->id);

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user2->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_has_multiple_likes()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);
        $entity->like(2);
        $entity->like(3);
        $entity->like(4);

        $this->assertEquals(4, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_duplicate_likes()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);
        $entity->like(1);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_unlike()
    {
        $entity = factory(Entity::class)->create();
        $entity->like(1);

        $entity->unlike(1);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_unlike_by_user_if_not_liked()
    {
        $entity = factory(Entity::class)->create();
        $entity->like(1);

        $entity->unlike(2);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_add_like_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->likeToggle();

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_remove_like_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity->like();

        $entity->likeToggle();

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_add_like_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeToggle(1);
        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_remove_like_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->like(1);

        $entity->likeToggle(1);
        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->like();

        $this->assertTrue($entity->liked());
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->like(1);

        $this->assertTrue($entity->liked(1));
        $this->assertFalse($entity->liked(2));
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_current_user_using_attribute()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->like();

        $this->assertTrue($entity->liked);
    }

    /** @test */
    public function it_can_get_where_liked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        factory(Entity::class)->create()->like($user->id);
        factory(Entity::class)->create()->like($user->id);
        factory(Entity::class)->create()->like($user->id);

        $likedEntities = Entity::whereLikedBy()->get();

        $this->assertCount(3, $likedEntities);
    }

    /** @test */
    public function it_can_get_where_liked_by_concrete_user()
    {
        factory(Entity::class)->create()->like(1);
        factory(Entity::class)->create()->like(1);
        factory(Entity::class)->create()->like(1);

        $likedEntities = Entity::whereLikedBy(1)->get();
        $shouldBeEmpty = Entity::whereLikedBy(2)->get();

        $this->assertCount(3, $likedEntities);
        $this->assertEmpty($shouldBeEmpty);
    }

    /* Dislikes */

    /** @test */
    public function it_can_dislike_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->dislike();

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_dislike_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $entity->dislike($user2->id);

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user2->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_has_multiple_dislikes()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislike(1);
        $entity->dislike(2);
        $entity->dislike(3);
        $entity->dislike(4);

        $this->assertEquals(4, $entity->dislikesCount);
    }

    /** @test */
    public function it_cannot_duplicate_dislikes()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislike(1);
        $entity->dislike(1);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_undislike()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislike(1);

        $entity->undislike(1);

        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_cannot_undislike_by_user_if_not_disliked()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislike(1);

        $entity->undislike(2);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_add_dislike_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->dislikeToggle();

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_remove_dislike_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity->dislike();

        $entity->dislikeToggle();

        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_add_dislike_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislikeToggle(1);
        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_remove_dislike_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislike(1);

        $entity->dislikeToggle(1);
        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->dislike();

        $this->assertTrue($entity->disliked());
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislike(1);

        $this->assertTrue($entity->disliked(1));
        $this->assertFalse($entity->disliked(2));
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_current_user_using_attribute()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->dislike();

        $this->assertTrue($entity->disliked);
    }

    /** @test */
    public function it_can_get_where_disliked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        factory(Entity::class)->create()->dislike($user->id);
        factory(Entity::class)->create()->dislike($user->id);
        factory(Entity::class)->create()->dislike($user->id);

        $dislikedEntities = Entity::whereDislikedBy()->get();

        $this->assertCount(3, $dislikedEntities);
    }

    /** @test */
    public function it_can_get_where_disliked_by_concrete_user()
    {
        factory(Entity::class)->create()->dislike(1);
        factory(Entity::class)->create()->dislike(1);
        factory(Entity::class)->create()->dislike(1);

        $dislikedEntities = Entity::whereDislikedBy(1)->get();
        $shouldBeEmpty = Entity::whereDislikedBy(2)->get();

        $this->assertCount(3, $dislikedEntities);
        $this->assertEmpty($shouldBeEmpty);
    }

    /* Likes & Dislikes */

    /** @test */
    public function it_can_get_likes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);

        $this->assertInstanceOf(LikeContract::class, $entity->likes->first());
        $this->assertCount(1, $entity->likes);
    }

    /** @test */
    public function it_can_get_dislikes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislike(1);

        $this->assertInstanceOf(LikeContract::class, $entity->dislikes->first());
        $this->assertCount(1, $entity->dislikes);
    }

    /** @test */
    public function it_can_get_dislikes_and_likes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);
        $entity->dislike(2);

        $this->assertInstanceOf(LikeContract::class, $entity->likesAndDislikes->first());
        $this->assertCount(2, $entity->likesAndDislikes);
    }

    /** @test */
    public function it_can_get_likes_minus_dislikes_difference()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);
        $entity->dislike(2);
        $entity->dislike(3);

        $this->assertEquals(-1, $entity->likesDiffDislikesCount);
    }
}
