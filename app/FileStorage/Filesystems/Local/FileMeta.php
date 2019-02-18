<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\Plus\FileStorage\Filesystems\Local;

use Closure;
use Exception;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\FileStorage\ImageDimension;
use Zhiyi\Plus\FileStorage\FileMetaAbstract;
use Zhiyi\Plus\FileStorage\Pay\PayInterface;
use Zhiyi\Plus\FileStorage\ResourceInterface;
use Zhiyi\Plus\FileStorage\Traits\HasImageTrait;
use Zhiyi\Plus\FileStorage\ImageDimensionInterface;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class FileMeta extends FileMetaAbstract
{
    use HasImageTrait;

    /**
     * Local filesystem.
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Resource instance.
     * @var \Zhiyi\Plus\FileStorage\ResourceInterface
     */
    protected $resource;

    /**
     * Cache the instance image dimension.
     * @var \Zhiyi\Plus\FileStorage\ImageDimensionInterface
     */
    protected $dimension;

    /**
     * Create a file meta.
     * @param \Illuminate\Contracts\Filesystem\Filesystem $filesystem
     * @param \Zhiyi\Plus\FileStorage\ResourceInterface $resource
     */
    public function __construct(FilesystemContract $filesystem, ResourceInterface $resource)
    {
        $this->filesystem = $filesystem;
        $this->resource = $resource;
        $this->hasImage();
    }

    /**
     * Use custom MIME types.
     * @return null|\Closure
     */
    protected function useCustomTypes(): ?Closure
    {
        return function () {
            return [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ];
        };
    }

    /**
     * Has the file is image.
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->hasImageType(
            $this->getMimeType()
        );
    }

    /**
     * Get image file dimension.
     * @return \Zhiyi\Plus\FileStorage\ImageDimensionInterface
     */
    public function getImageDimension(): ImageDimensionInterface
    {
        if (! $this->hasImage()) {
            throw new Exception('调用的资源并非图片或者是不支持的图片资源');
        } elseif ($this->dimension instanceof ImageDimensionInterface) {
            return $this->dimension;
        }

        $realPath = $this->filesystem->path(
            $this->resource->getPath()
        );
        list($width, $height) = getimagesize($realPath);

        return $this->dimension = new ImageDimension((float) $width, (float) $height);
    }

    /**
     * Get the file size (Byte).
     * @return int
     */
    public function getSize(): int
    {
        return $this->filesystem->getSize(
            $this->resource->getPath()
        );
    }

    /**
     * Get the resource mime type.
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->filesystem->mimeType(
            $this->resource->getPath()
        ) ?: 'application/octet-stream';
    }

    /**
     * Get the resource pay info.
     * @param \Zhiyi\Plus\Models\User $user
     * @return \Zhiyi\Plus\FileStorage\Pay\PayInterface
     */
    public function getPay(User $user): ?PayInterface
    {
        return null;
    }

    /**
     * Get the storage vendor name.
     * @return string
     */
    public function getVendorName(): string
    {
        return 'local';
    }

    /**
     * Get the resource url.
     * @return string
     */
    public function url(): string
    {
        return route('storage:get', [
            'channel' => $this->resource->getChannel(),
            'path' => base64_encode($this->resource->getPath()),
        ]);
    }
}
