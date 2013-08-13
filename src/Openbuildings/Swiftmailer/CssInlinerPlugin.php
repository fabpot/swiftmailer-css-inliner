<?php

namespace Openbuildings\Swiftmailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * @package    Openbuildings\Swiftmailer
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class CssInlinerPlugin implements \Swift_Events_SendListener
{
	/**
	 * @param Swift_Mime_Message $message
	 * @param string $mime_type
	 * @return Swift_Mime_MimePart
	 */
	protected function getMIMEPart(\Swift_Mime_Message $message, $mime_type) 
	{
		$part_content = NULL;
		foreach ($message->getChildren() as $part) 
		{
			if (strpos($part->getContentType(), $mime_type) === 0)
			{
				$part_content = $part;
			}
		}
		return $part_content;
	}

	/**
	 * @param Swift_Events_SendEvent $evt
	 */
	public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
	{
		$message = $evt->getMessage();

		$converter = new CssToInlineStyles();
		$converter->setEncoding($message->getCharset());
		$converter->setUseInlineStylesBlock(TRUE);

		if ($message->getContentType() === 'text/html') 
		{
			$converter->setCSS('');
			$converter->setHTML($message->getBody());

			$message->setBody($converter->convert());
		}

		foreach ($message->getChildren() as $part) 
		{
			if (strpos($part->getContentType(), 'text/html') === 0)
			{
				$converter->setCSS('');
				$converter->setHTML($part->getBody());

				$part->setBody($converter->convert());
			}
		}
	}

	/**
	 * Do nothing
	 *
	 * @param Swift_Events_SendEvent $evt
	 */
	public function sendPerformed(\Swift_Events_SendEvent $evt)
	{
		// Do Nothing
	}
}
