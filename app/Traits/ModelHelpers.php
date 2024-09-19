<?php

namespace App\Traits;

trait ModelHelpers
{
    // Accessors

    public function getResourceUrlAttribute()
    {
        return $this->getResourceUrl();
    }

    public function getParentResourceUrlAttribute()
    {
        return $this->getParentResourceUrl();
    }

    public function getColorNameAttribute()
    {
        return ($this->color ? $this->color->name : '');
    }


    // Helper methods

    public function getResourceUrl($action = 'show', $isAdminLink = true)
    {
        $routeResourceName = isset($this->routeResourceName) ? $this->routeResourceName : ($this->getTrimmedClassName() . "s");
        $route = route($routeResourceName . '.' . $action, $this->getKey());
        $route = $isAdminLink ? $route : str_replace(config('app.url'), config('app.publisher.url'), $route);

        return $route;
    }

    public function getParentResourceUrl($action = 'show', $isAdminLink = true)
    {
        return isset($this->parentResource) ? $this->parentResource->getResourceUrl($action = 'show', $isAdminLink = true) : route('dashboard');
    }

    protected function getTrimmedClassName()
    {
        return strtolower(@end(explode('\\', get_class($this))));
    }

    public function getResourceName()
    {
        return (ucwords(class_basename(get_class($this))));
    }

    public function getResourceNameAttribute()
    {
        $className = class_basename(get_class($this));
        return ucwords(preg_replace('/(?=[A-Z])/', ' ', ($className)));
        // return (ucwords(class_basename(get_class($this))));
    }

    public function getIdentifierAttribute()
    {
        return "#{$this->getKey()}";
    }

    public function getBase64($media)
    {
        if (!$media) return;

        try {
            return 'data:' . $media->mime_type . ';base64, ' . base64_encode(file_get_contents($media->getPath()));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setJsonValue($json, $attribute, $value)
    {
        if (!$value) return;

        $data = $this->attributes[$json] ? json_decode($this->attributes[$json], true) : [];
        $data[$attribute] = $value;
        $this->attributes[$json] = json_encode($data);
    }


    public function markAllNotificationsAsRead()
    {
        $notifications = $this->unreadNotifications->where('data.for', 'admin');
        if ($notifications) $notifications->markAsRead();
    }
}
