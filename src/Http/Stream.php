<?php

namespace Simples\Core\Http;

use Psr\Http\Message\StreamInterface;
use \InvalidArgumentException;
use \RuntimeException;
use \Exception;

/**
 * Class Stream
 * @package Simples\Core\Http
 */
class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var string|resource
     */
    protected $stream;

    /**
     * @param string|resource $stream
     * @param string $mode Mode with which to open stream
     * @throws InvalidArgumentException
     */
    public function __construct($stream, $mode = 'r')
    {
        $this->stream = $stream;

        if (is_resource($stream)) {

            $this->resource = $stream;

        } else if (is_string($stream)) {

            try {
                $this->resource = fopen($this->stream, $mode);

            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    $e->getMessage()
                );
            }

        } else {
            throw new InvalidArgumentException(
                'Invalid stream provided; must be a string stream identifier or resource'
            );
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        if (!$this->isReadable()) {
            return '';
        }
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (!$this->resource) {
            return;
        }
        $resource = $this->detach();

        fclose($resource);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Attach a new stream/resource to the instance.
     *
     * @param string|resource $resource
     * @param string $mode
     * @throws InvalidArgumentException for stream identifier that cannot be
     *     cast to a resource
     * @throws InvalidArgumentException for non-resource stream
     */
    public function attach($resource, $mode = 'r')
    {
        $error = null;
        if (!is_resource($resource) && is_string($resource)) {
            set_error_handler(function ($e) use (&$error) {
                $error = $e;
            }, E_WARNING);
            $resource = fopen($resource, $mode);
            restore_error_handler();
        }
        if ($error) {
            throw new InvalidArgumentException('Invalid stream reference provided');
        }
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(
                'Invalid stream provided; must be a string stream identifier or resource'
            );
        }
        $this->resource = $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (null === $this->resource) {
            return null;
        }
        $stats = fstat($this->resource);
        return $stats['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot tell position');
        }
        $result = ftell($this->resource);
        if (!is_int($result)) {
            throw new RuntimeException('Erro occurred during tell operation');
        }
        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if (!$this->resource) {
            return true;
        }
        return feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        if (!$this->resource) {
            return false;
        }
        $meta = stream_get_meta_data($this->resource);
        return $meta['seekable'];
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     * @return bool
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot seek position');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }
        $result = fseek($this->resource, $offset, $whence);
        if (0 !== $result) {
            throw new RuntimeException('Erro seeking within stream');
        }
        return true;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if (!$this->resource) {
            return false;
        }
        $meta = stream_get_meta_data($this->resource);
        return is_writable($meta['uri']);
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot write');
        }

        $result = fwrite($this->resource, $string);

        if (false === $result) {
            throw new RuntimeException('Erro writing to stream');
        }
        return $result;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if (!$this->resource) {
            return false;
        }
        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];
        return (strstr($mode, 'r') || strstr($mode, '+'));
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot read');
        }
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = fread($this->resource, $length);
        if (false === $result) {
            throw new RuntimeException('Erro reading stream');
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            return '';
        }
        rewind($this->resource);

        $result = stream_get_contents($this->resource);
        if (!$result) {
            return '';
        }
        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return stream_get_meta_data($this->resource);
        }
        $metadata = stream_get_meta_data($this->resource);
        if (!array_key_exists($key, $metadata)) {
            return null;
        }
        return $metadata[$key];
    }
}