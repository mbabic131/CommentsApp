<?php

namespace AppBundle\Repository;

/**
 * AuthorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthorRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Set slug for author
	 *
	 * @param string $name
	 *
	 * @return string 
	 */
	public function setSlug($name)
	{
		$slug = str_replace(' ', '', trim($name));

		return strtolower($slug);
	}
}
