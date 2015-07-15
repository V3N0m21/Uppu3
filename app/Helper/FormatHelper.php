<?php namespace Uppu3\Helper;

class FormatHelper
{
	static public function formatSize($bytes)
	{
		if ($bytes >= 1024*1024*1024)
		{
			$bytes = number_format($bytes / (1024*1024*1024), 2) . ' GB';
		}
		elseif ($bytes >= 1024*1024)
		{
			$bytes = number_format($bytes / (1024*1024), 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		}

		return $bytes;
	}
	static public function formatPreviewLink($id, $name)
	{
		return "/upload/resize/resize-$id-$name";
	}
	static public function formatDownloadLink($id,$name)
	{
		$name = urlencode($name);
		return "/download/$id/$name";
	}

	static public function formatDownloadFile($id, $name)
	{
		return "upload/".$id."-".$name."-txt";
	}
	static public function formatUploadLink($id, $name)
	{
		return $_SERVER['DOCUMENT_ROOT']."/upload/".$id."-".$name."-txt";
	}

	static public function formatUploadResizeLink($id, $name)
	{
		return $_SERVER['DOCUMENT_ROOT']."/upload/resize/resize-".$id."-".$name;
	}

	static public function isPicture($extension)
	{
		$pictures = array('image/jpeg','image/gif','image/png');
		if (in_array($extension, $pictures)) {
			return true;
		} else {
			return false;
		}

	}

	static public function isVideo($extension)
	{
		$video = array('video/webm', 'video/mp4');
		if (in_array($extension, $video)) {
			return true;
		} else {
			return false;
		}

	}
}